<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\EncryptionException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\ApplicationMetadataMismatchException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FieldNotFoundException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FieldTypeMismatchException;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FormNotFoundException;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationService
{
    private const CREATE_REFERENCE_MAX_TRIES = 3;

    public function __construct(
        private SubsidyRepository $subsidyRepository,
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $appRepo,
        private ApplicationFileRepository $fileRepository,
        private ApplicationReferenceService $applicationReferenceService,
        private IdentityService $identityService,
        private FrontendDecryption $frontendDecryptionService,
    ) {
    }

    private function validateUuid(string $uuid): void
    {
        if (!Str::isUuid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID');
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
        if ($application->identity->id !== $identity->id) {
            throw new EncryptionException(
                sprintf('Identity mismatch for app with identifier "%s"', $application->id)
            );
        }

        if (!$application->status->isEditableForApplicant()) {
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
        $app = $this->appRepo->makeApplicationForIdentityAndSubsidyVersion($identity, $subsidyStage->subsidyVersion);
        $app->id = $id;
        $app->application_title = $subsidyStage->title;
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
     *
     * @return array{ApplicationStage, SubsidyStage}
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

    private function processFieldValue(ApplicationStage $applicationStage, FieldValue $value): void
    {
        // answer for file already exists at this point
        if ($value->field->type === FieldType::Upload) {
            return;
        }

        $this->createOrUpdateAnswer($applicationStage, $value->field, $value->value);
    }

    public function processFieldValues(ApplicationStage $applicationStage, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->processFieldValue($applicationStage, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    public function processFormSubmit(FormSubmit $formSubmit): void
    {
        DB::connection(Connection::APPLICATION)->transaction(function () use ($formSubmit) {
            $identity = $this->identityService->findOrCreateIdentity($formSubmit->identity);
            $json = $this->frontendDecryptionService->decrypt($formSubmit->encryptedData);

            [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
                $identity,
                $formSubmit->applicationMetadata
            );

            $values = $this->decodingService->decodeFormValues($subsidyStage, $json);

            // TODO: Validation will be in other PR
            $this->processFieldValues($applicationStage, $values);

            $applicationStage->application->status = ApplicationStatus::Submitted;
            $applicationStage->application->final_review_deadline =
                Carbon::now()->addDays($applicationStage->application->subsidyVersion->review_period);
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
        $identity = $this->identityService->findOrCreateIdentity($fileUpload->identity);

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

        $encryptedContents = $this->encryptionService->encryptData($decryptedContents);
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
}
