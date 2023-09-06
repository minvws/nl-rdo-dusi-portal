<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use DateTimeImmutable;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\ApplicationMetadataMismatchException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\EncryptionException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FieldNotFoundException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FieldTypeMismatchException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FileNotFoundException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FormNotFoundException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FormSubmitInvalidBodyReceivedException;
use MinVWS\DUSi\Application\Backend\Services\Validation\Validator;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Models\IdentityType;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListApplication;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Form;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity as SerialisationIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings("LongVariable")
 */

readonly class ApplicationService
{
    public function __construct(
        private SubsidyRepository $subsidyRepository,
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $appRepo,
        private ApplicationFileRepository $fileRepository,
        private ValidationService $validationService,
    ) {
    }

    private function validateUuid(string $uuid): void
    {
        if (!Str::isUuid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID');
        }
    }

    private function loadApplicationStageIfExists(string $applicationStageId): ?ApplicationStage
    {
        $this->validateUuid($applicationStageId);
        return $this->appRepo->getApplicationStage($applicationStageId);
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
                sprintf('Identity mismatch for app with identifier "%s"', $applicationStage->id)
            );
        }

        if ($applicationStage->subsidy_stage_id !== $appMetadata->subsidyStageId) {
            throw new ApplicationMetadataMismatchException(
                sprintf('Form mismatch for app with identifier "%s', $applicationStage->id)
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
    private function createApplication(Identity $identity, SubsidyStage $subsidyStage): Application
    {
        if (!isset($subsidyStage->subsidyVersion)) {
            throw new RuntimeException('SubsidyVersion is not set');
        }
        $app = $this->appRepo->makeApplicationForSubsidyVersion($subsidyStage->subsidyVersion);
        $app->application_title = $subsidyStage->title;
        $app->identity = $identity;
        $this->appRepo->saveApplication($app);
        return $app;
    }

    private function createApplicationStage(
        string $appMetadataId,
        Identity $identity,
        SubsidyStage $subsidyStage
    ): ApplicationStage {
        $this->validateUuid($appMetadataId);
        $app = $this->createApplication($identity, $subsidyStage);
        $applicationStage = $this->appRepo->makeApplicationStage($app, $subsidyStage);
        $applicationStage->id = $appMetadataId;
        $applicationStage->stage = $subsidyStage->stage;
        $this->appRepo->saveApplicationStage($applicationStage);
        return $applicationStage;
    }

    private function loadOrCreateApplicationStageVersion(ApplicationStage $applicationStage): ApplicationStageVersion
    {
        $latestApplicationStageVersion = $this->appRepo->getLatestApplicationStageVersion($applicationStage);
        if (!isset($latestApplicationStageVersion)) {
            $applicationStageVersion = $this->appRepo->makeApplicationStageVersion($applicationStage);
            $applicationStageVersion->version = 0;
            $applicationStageVersion->status = ApplicationStageVersionStatus::Draft;
            $this->appRepo->saveApplicationStageVersion($applicationStageVersion);
            return $applicationStageVersion;
        }
        if ($latestApplicationStageVersion->status === ApplicationStageVersionStatus::Draft) {
            return $latestApplicationStageVersion;
        }
        $applicationStageVersion = $this->appRepo->makeApplicationStageVersion($applicationStage);
        $applicationStageVersion->version = $latestApplicationStageVersion->version + 1;
        $applicationStageVersion->status = ApplicationStageVersionStatus::Draft;

        $answers = array_map(
            function (Answer $answer) use ($applicationStageVersion) {
                $replicatedAnswer = $answer->replicate(['id']);
                $replicatedAnswer->ApplicationStageVersion()->associate($applicationStageVersion);
                $this->appRepo->saveAnswer($replicatedAnswer);
                return $replicatedAnswer;
            },
            $latestApplicationStageVersion->answers()->get()->all()
        );
        $applicationStageVersion->answers()->saveMany($answers);
        $this->appRepo->saveApplicationStageVersion($applicationStageVersion);
        return $applicationStageVersion;
    }

    /**
     * @throws Throwable
     */
    private function loadOrCreateAppStageWithSubsidyStage(Identity $identity, ApplicationMetadata $appMetadata): array
    {
        $applicationStage = $this->loadApplicationStageIfExists($appMetadata->applicationStageId);
        $subsidyStage = $this->loadSubsidyStage($appMetadata->subsidyStageId);
        if (!isset($subsidyStage)) {
            throw new Exception('SubsidyStage is not set');
        }

        if (!isset($applicationStage)) {
            $applicationStage = $this->createApplicationStage(
                $appMetadata->applicationStageId,
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

    /**
     * @throws Exception
     */
    private function createOrUpdateAnswer(
        ApplicationStageVersion $applicationStageVersion,
        Field $field,
        mixed $value
    ): void {
        $answer = $this->appRepo->makeAnswer($applicationStageVersion, $field);
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
    private function processFieldValue(ApplicationStageVersion $applicationStageVersion, FieldValue $value): void
    {
        // answer for file already exists at this point
        if ($value->field->type === FieldType::Upload) {
            return;
        }

        $this->createOrUpdateAnswer($applicationStageVersion, $value->field, $value->value);
    }

    private function processFieldValues(ApplicationStageVersion $applicationStageVersion, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->processFieldValue($applicationStageVersion, $fieldValue);
        }
    }

    /**
     *
     */
    private function getIdentityFromSerialisation(SerialisationIdentity $serialisationIdentity): Identity
    {
        return new Identity(
            IdentityType::from($serialisationIdentity->type->value),
            $serialisationIdentity->identifier,
        );
    }

    /**
     * @throws Throwable
     */
    public function processFormSubmit(FormSubmit $formSubmit): ApplicationStage
    {
        $applicationStage = DB::connection(Connection::APPLICATION)->transaction(function () use ($formSubmit) {
            $formSubmit = $this->encryptionService->decryptFormSubmit($formSubmit);
            $json = $formSubmit->encryptedData;

            [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
                $this->getIdentityFromSerialisation($formSubmit->identity),
                $formSubmit->applicationMetadata
            );

            try {
                $values = $this->decodingService->decodeFormValues($subsidyStage, $json);
            } catch (Throwable $exception) {
                throw new FormSubmitInvalidBodyReceivedException(
                    message: 'Form submit invalid, could not decode form values',
                    previous: $exception,
                );
            }

            $applicationStageVersion = $this->loadOrCreateApplicationStageVersion($applicationStage);

            $validator = $this->validationService->getValidator($applicationStageVersion, $values);
            $applicationStageVersion->status = ApplicationStageVersionStatus::Submitted;
            if ($validator->fails()) {
                $applicationStageVersion->status = ApplicationStageVersionStatus::Invalid;
            }

            $this->processInvalidFieldValues($applicationStageVersion, $values, $validator);
            $this->processFieldValues($applicationStageVersion, $values);

            $this->appRepo->saveApplicationStageVersion($applicationStageVersion);
            $this->appRepo->saveApplicationStage($applicationStage);

            return $applicationStage;
        });
        return $applicationStage;
    }

    /**
     * @throws Throwable
     */
    private function doProcessFileUpload(FileUpload $fileUpload): void
    {
        $fileUpload = $this->encryptionService->decryptFileUpload($fileUpload);
        [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
            $this->getIdentityFromSerialisation($fileUpload->identity),
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
        $encryptedContents = $fileUpload->encryptedContents; // TODO: encrypt based on app specific key

        $value = [
            'mimeType' => $fileUpload->mimeType,
            'extension' => $fileUpload->extension,
            'size' => $size
        ];

        $applicationStageVersion = $this->loadOrCreateApplicationStageVersion($applicationStage);

        [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
            $this->getIdentityFromSerialisation($fileUpload->identity),
            $fileUpload->applicationMetadata
        );

        $this->createOrUpdateAnswer(
            $applicationStageVersion,
            $field,
            $value
        );

        $encryptedContents = $this->encryptionService->encryptData($encryptedContents);
        $result = $this->fileRepository->writeFile($applicationStage, $field, $encryptedContents);
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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function listApplications(ApplicationListParams $params): ApplicationList
    {
        // TODO: fill list based on the applications in `applications`
        return new ApplicationList([
            new ApplicationListApplication(
                Uuid::uuid4()->toString(),
                new Subsidy(Uuid::uuid4()->toString(), 'Voorbeeld subsidie', 'https://www.dus-i.nl/'),
                new DateTimeImmutable(),
                new DateTimeImmutable("+30 days"),
                ApplicationStatus::New
            )
        ]);
    }

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        // TODO:
        // Retrieve application from the database and verify the identity. If the
        // application doesn't exist or doesn't belong to the user return a not found
        // response. If the application exists, retrieve all the answers, decrypt
        // them and structure them in the correct JSON format (compatible with the schema).
        $application = new ApplicationDTO(
            $params->id,
            new Subsidy(Uuid::uuid4()->toString(), 'Voorbeeld subsidie', 'https://www.dus-i.nl/'),
            new DateTimeImmutable(),
            new DateTimeImmutable("+30 days"),
            ApplicationStatus::New,
            new Form(Uuid::uuid4()->toString(), 1),
            $params->includeData ? (object)['firstName' => 'John', 'lastName' => 'Doe'] : null
        );

        return $this->encryptionService->encryptResponse(
            EncryptedResponseStatus::OK,
            $application,
            $params->publicKey
        );
    }

    protected function processInvalidFieldValues(
        ApplicationStageVersion $applicationStageVersion,
        array $values,
        Validator $validator,
    ): void {
        // TODO: Process invalid fields ...
        // TODO: Reset values for invalid fields ...
    }
}
