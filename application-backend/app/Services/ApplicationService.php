<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use DateInterval;
use DateTimeImmutable;
use Illuminate\Database\QueryException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListApplication;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Form;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\EncryptionException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\ApplicationMetadataMismatchException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FieldNotFoundException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FieldTypeMismatchException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FileNotFoundException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FormNotFoundException;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Subsidy;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Exception;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings("LongVariable")
 */
readonly class ApplicationService
{
    private const CREATE_REFERENCE_MAX_TRIES = 3;

    public function __construct(
        private SubsidyRepository $subsidyRepository,
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $appRepo,
        private FilesystemManager $filesystemManager,
        private ApplicationReferenceService $applicationReferenceService,
    ) {
    }

    private function validateUuid(string $uuid): void
    {
        if (!Str::isUuid($uuid)) {
            throw new \InvalidArgumentException('Invalid UUID');
        }
    }

    private function loadApplicationStageIfExists(string $applicationId): ?ApplicationStage
    {
        $this->validateUuid($applicationId);
        $application = $this->appRepo->getApplication($applicationId);
        return $application?->currentApplicationStage;
    }

    /**
     * @throws ApplicationMetadataMismatchException|EncryptionException
     */
    private function validateIdentityAndApplicationMetadata(
        Identity $identity,
        ApplicationMetadata $appMetadata,
        ApplicationStage $applicationStage
    ): void {
        $application = $applicationStage->application;
        if (
            $application->identity->type !== $identity->type || // @phpstan-ignore-line
            $application->identity->identifier !== $identity->identifier
        ) {
            throw new EncryptionException(
                sprintf('Identity mismatch for app with identifier "%s"', $application->id)
            );
        }

        if (!in_array($application->status, [ApplicationStatus::Draft, ApplicationStatus::RequestForChanges])) {
            throw new ApplicationMetadataMismatchException(
                sprintf('Current status does not allow editing for "%s', $application->id)
            );
        }

        if ($applicationStage->subsidy_stage_id !== $appMetadata->subsidyStageId) {
            throw new ApplicationMetadataMismatchException(
                sprintf('Form mismatch for app with identifier "%s', $application->id)
            );
        }
    }

    /**
     * @throws FormNotFoundException
     */
    private function loadSubsidyStage(string $subsidyStageId): ?SubsidyStage
    {
        $this->validateUuid($subsidyStageId);
        return $this->subsidyRepository->getSubsidyStage($subsidyStageId);
    }

    /**
     * @throws Exception
     */
    public function createApplication(string $id, Identity $identity, SubsidyStage $subsidyStage): Application
    {
        $app = $this->appRepo->makeApplicationForSubsidyVersion($subsidyStage->subsidyVersion);
        $app->id = $id;
        $app->application_title = $subsidyStage->title;
        $app->identity = $identity;
        $app->status = ApplicationStatus::Draft;

        $createReferenceTries = 0;
        do {
            try {
                DB::transaction(function () use ($app) {
                    $this->saveApplication($app);
                });
                return $app;
            } catch (QueryException $queryException) {
                //We assume a QueryException is caused by a Duplicate entry exception on the unique constraint of the
                //reference field. We try again until CREATE_REFERENCE_MAX_TRIES.
            }
        } while (self::CREATE_REFERENCE_MAX_TRIES > $createReferenceTries++);

        throw $queryException;
    }

    private function saveApplication(Application $application): void
    {
        $application->reference = $this->applicationReferenceService->generateUniqueReferenceByElevenRule(
            $application->subsidyVersion->subsidy
        );
        $this->appRepo->saveApplication($application);
    }

    private function createApplicationStage(
        string $applicationId,
        Identity $identity,
        SubsidyStage $subsidyStage
    ): ApplicationStage {
        $this->validateUuid($applicationId);
        $app = $this->createApplication($applicationId, $identity, $subsidyStage);
        $applicationStage = $this->appRepo->makeApplicationStage($app, $subsidyStage);
        $applicationStage->sequence_number = 1;
        $applicationStage->is_current = true;
        $this->appRepo->saveApplicationStage($applicationStage);
        return $applicationStage;
    }

    /**
     * @throws Throwable
     */
    private function loadOrCreateAppStageWithSubsidyStage(Identity $identity, ApplicationMetadata $appMetadata): array
    {
        $subsidyStage = $this->loadSubsidyStage($appMetadata->subsidyStageId);
        if (!isset($subsidyStage)) {
            throw new Exception('Invalid subsidy stage');
        }

        $applicationStage = $this->loadApplicationStageIfExists($appMetadata->applicationId);
        if ($applicationStage === null) {
            $applicationStage = $this->createApplicationStage(
                $appMetadata->applicationId,
                $identity,
                $subsidyStage
            );
        }

        $this->validateIdentityAndApplicationMetadata($identity, $appMetadata, $applicationStage);

        return [$applicationStage, $subsidyStage];
    }

    /**
     * @throws FieldNotFoundException
     */
    private function loadField(SubsidyStage $subsidyStage, string $fieldCode): Field
    {
        $field = $this->subsidyRepository->getFieldForSubsidyStageAndCode($subsidyStage, $fieldCode);
        if ($field === null) {
            throw new FieldNotFoundException(
                sprintf(
                    'Field with code "%s" not found for form with identifier "%s"!',
                    $fieldCode,
                    $subsidyStage->id
                )
            );
        }
        return $field;
    }

    private function getFilePath(ApplicationStage $applicationStage, Field $field): string
    {
        return sprintf('%s/%s', $applicationStage->id, $field->code);
    }

    /**
     * @throws FileNotFoundException
     */
    private function validateFileAnswer(
        ApplicationStage $applicationStage,
        Field $field
    ): void {
        $answer = $this->appRepo->getAnswer($applicationStage, $field);
        if ($answer === null) {
            throw new FileNotFoundException("Answer for file {$field->code} not found!");
        }

        $path = $this->getFilePath($applicationStage, $field);
        if (!$this->filesystemManager->disk(Disk::APPLICATION_FILES)->exists($path)) {
            throw new FileNotFoundException("File not found!");
        }
    }

    /**
     * @throws Exception
     */
    private function createOrUpdateAnswer(
        ApplicationStage $applicationStage,
        Field $field,
        mixed $value
    ): void {
        $answer = $this->appRepo->makeAnswer($applicationStage, $field);
        $json = json_encode($value, JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) {
            throw new Exception('JSON encoding failed. Invalid data provided.');
        }
        $answer->encrypted_answer = $this->encryptionService->encryptData($json);
        $this->appRepo->saveAnswer($answer);
    }

    /**
     * @throws FileNotFoundException
     */
    private function processFieldValue(ApplicationStage $applicationStage, FieldValue $value): void
    {
        // answer for file already exists at this point
        if ($value->field->type !== FieldType::Upload) {
            $this->createOrUpdateAnswer($applicationStage, $value->field, $value->value);
        }
    }

    private function processFieldValues(ApplicationStage $applicationStage, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->processFieldValue($applicationStage, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValue(
        ApplicationStage $applicationStage,
        FieldValue $value
    ): void {
        if ($value->field->type === FieldType::Upload) {
            $this->validateFileAnswer($applicationStage, $value->field);
        }

        // TODO: validate format of certain fields
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValues(
        ApplicationStage $applicationStage,
        array $fieldValues
    ): void {
        foreach ($fieldValues as $fieldValue) {
            $this->validateFieldValue($applicationStage, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    public function processFormSubmit(FormSubmit $formSubmit): void
    {
        DB::connection(Connection::APPLICATION)->transaction(function () use ($formSubmit) {
            $identity = $this->encryptionService->decryptIdentity($formSubmit->identity);
            $json = $this->encryptionService->decryptBase64EncodedData($formSubmit->encryptedData);

            [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
                $identity,
                $formSubmit->applicationMetadata
            );

            $values = $this->decodingService->decodeFormValues($subsidyStage, $json);

            $this->validateFieldValues($applicationStage, $values);
            $this->processFieldValues($applicationStage, $values);

            $applicationStage->application->status = ApplicationStatus::Submitted;
            $this->appRepo->saveApplication($applicationStage->application);

            $applicationStage->is_current = false;
            $this->appRepo->saveApplicationStage($applicationStage);

            // TODO: make next application stage
            // $this->appRepo->makeApplicationStage($applicationStage->application, $nextSubsidyStage);
        });
    }

    /**
     * @throws Throwable
     */
    private function doProcessFileUpload(FileUpload $fileUpload): void
    {
        $identity = $this->encryptionService->decryptIdentity($fileUpload->identity);

        [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
            $identity,
            $fileUpload->applicationMetadata
        );

        $field = $this->loadField($subsidyStage, $fileUpload->fieldCode);
        if ($field->type !== FieldType::Upload) {
            throw new FieldTypeMismatchException(
                sprintf(
                    'Field "%s" type mismatch, expected: %s, actual: %s',
                    $field->code,
                    FieldType::Upload->value,
                    $field->type->value
                )
            );
        }

        $size = strlen($fileUpload->encryptedContents);
        $decryptedContents = $this->encryptionService->decryptBase64EncodedData($fileUpload->encryptedContents);

        $value = [
            'mimeType' => $fileUpload->mimeType,
            'extension' => $fileUpload->extension,
            'size' => $size
        ];

        $this->createOrUpdateAnswer(
            $applicationStage,
            $field,
            $value
        );

        $path = $this->getFilePath($applicationStage, $field);
        $encryptedContents = $this->encryptionService->encryptData($decryptedContents);
        $result = $this->filesystemManager->disk(Disk::APPLICATION_FILES)->put($path, $encryptedContents);
        if (!$result) {
            throw new Exception('Failed to write file to disk!');
        }
    }

    /**
     * @throws Throwable
     */
    public function processFileUpload(FileUpload $fileUpload): void
    {
        DB::connection(Connection::APPLICATION)->transaction(fn () => $this->doProcessFileUpload($fileUpload));
    }

    private function toApplicationListApplication(Application $app): ApplicationListApplication
    {
        // TODO: where to retrieve / base deadline on?
        // TODO: application status
        $subsidy = new Subsidy(
            $app->subsidyVersion->subsidy->code,
            $app->subsidyVersion->title,
            $app->subsidyVersion->subsidy_page_url
        );

        return new ApplicationListApplication(
            $app->reference,
            $subsidy,
            $app->created_at,
            DateTimeImmutable::createFromInterface($app->created_at)->add(new DateInterval('30D')),
            ApplicationStatus::Draft
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function listApplications(ApplicationListParams $params): EncryptedResponse
    {
        $identity = $this->encryptionService->decryptIdentity($params->identity);

        /** @var array<Application> $apps */
        $apps = $this->appRepo->getMyApplications($identity)->toArray();
        $listApps = array_map(fn ($app) => $this->toApplicationListApplication($app), $apps);

        return $this->encryptionService->encryptResponse(
            EncryptedResponseStatus::OK,
            new ApplicationList($listApps),
            $params->publicKey
        );
    }

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        // TODO:
        // Retrieve application from the database and verify the identity. If the
        // application doesn't exist or doesn't belong to the user return a not found
        // response. If the application exists, retrieve all the answers, decrypt
        // them and structure them in the correct JSON format (compatible with the schema).
        $application = new ApplicationDTO(
            $params->reference,
            new Subsidy(Uuid::uuid4()->toString(), 'Voorbeeld subsidie', 'https://www.dus-i.nl/'),
            new DateTimeImmutable(),
            new DateTimeImmutable("+30 days"),
            ApplicationStatus::Draft,
            new Form(Uuid::uuid4()->toString(), 1),
            $params->includeData ? (object)['firstName' => 'John', 'lastName' => 'Doe'] : null
        );

        return $this->encryptionService->encryptResponse(
            EncryptedResponseStatus::OK,
            $application,
            $params->publicKey
        );
    }
}
