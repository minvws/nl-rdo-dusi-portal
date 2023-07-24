<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationStage;

use App\Models\ApplicationStageVersion;
use App\Models\Disk;
use App\Models\Enums\ApplicationStageStatus;
use App\Repositories\ApplicationRepository;
use App\Repositories\FormRepository;
use App\Services\Exceptions\ApplicationIdentityMismatchException;
use App\Services\Exceptions\ApplicationMetadataMismatchException;
use App\Services\Exceptions\FieldNotFoundException;
use App\Services\Exceptions\FieldTypeMismatchException;
use App\Services\Exceptions\FileNotFoundException;
use App\Services\Exceptions\FormNotFoundException;

use App\Shared\Models\Application\FormSubmit;
use App\Shared\Models\Application\Identity;
use App\Shared\Models\Application\ApplicationMetadata;
use App\Shared\Models\Application\FileUpload;

use App\Shared\Models\Definition\SubsidyStage;
use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\Enums\FieldType;
use App\Models\Submission\FieldValue;


use App\Shared\Models\Connection;

use Exception;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Throwable;


readonly class ApplicationService
{
    public function __construct(
        private FormRepository $formRepository,
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository,
        private FilesystemManager $filesystemManager
    ) {
    }

    private function loadApplicationStageIfExists(string $applicationStageId): ?ApplicationStage
    {
        return $this->applicationRepository->getApplicationStage($applicationStageId);
    }

    /**
     * @throws ApplicationMetadataMismatchException|ApplicationIdentityMismatchException
     */
    private function validateIdentityAndApplicationMetadata(Identity $identity, ApplicationMetadata $applicationMetadata, ApplicationStage $applicationStage): void
    {
        if ($applicationStage->application->identity->type !== $identity->type || $applicationStage->application->identity->identifier !== $identity->identifier) {
            throw new ApplicationIdentityMismatchException(sprintf('Identity mismatch for application with identifier "%s"', $applicationStage->id));
        }

        if ($applicationStage->subsidy_stage_id !== $applicationMetadata->subsidyStageId) {
            throw new ApplicationMetadataMismatchException(sprintf('Form mismatch for application with identifier "%s', $applicationStage->id));
        }
    }

    /**
     * @throws FormNotFoundException
     */
    private function loadSubsidyStage(string $subsidyStageId): SubsidyStage
    {
        $subsidyStage = $this->formRepository->getSubsidyStage($subsidyStageId);
        if ($subsidyStage === null) {
            throw new FormNotFoundException(sprintf('Form with identifier "%s" does not exist!', $subsidyStageId));
        }
        return $subsidyStage;
    }

    private function createApplication(Identity $identity, SubsidyStage $subsidyStage): Application
    {
        $application = $this->applicationRepository->makeApplicationForSubsidyVersion($subsidyStage->subsidyVersion);
        $application->application_title = $subsidyStage->title;
        $application->identity = $identity;
        $this->applicationRepository->saveApplication($application);
        return $application;
    }

    private function createApplicationStage(string $applicationMetadataId, Identity $identity, SubsidyStage $subsidyStage): ApplicationStage
    {
        $application = $this->createApplication($identity, $subsidyStage);
        $applicationStage = $this->applicationRepository->makeApplicationStage($application, $subsidyStage);
        $applicationStage->id = $applicationMetadataId;
        $applicationStage->status = ApplicationStageStatus::Draft;
        $applicationStage->user_id = Uuid::uuid4()->toString();
        $this->applicationRepository->saveApplicationStage($applicationStage);
        return $applicationStage;
    }

    private function createApplicationStageVersion(ApplicationStage $applicationStage): ApplicationStageVersion
    {
        $applicationStageVersion = $this->applicationRepository->makeApplicationStageVersion($applicationStage);
        $applicationStageVersion->version = 0;
        $this->applicationRepository->saveApplicationStageVersion($applicationStageVersion);
        return $applicationStageVersion;
    }

    /**
     * @throws Throwable
     */
    private function loadOrCreateApplicationStageWithSubsidyStage(Identity $identity, ApplicationMetadata $applicationMetadata): array
    {

        $applicationStage = $this->loadApplicationStageIfExists($applicationMetadata->applicationStageId);

        if ($applicationStage !== null) {
            $this->validateIdentityAndApplicationMetadata($identity, $applicationMetadata, $applicationStage);
        }

        $subsidyStage = $this->loadSubsidyStage($applicationMetadata->subsidyStageId);

        if ($applicationStage === null) {
            $applicationStage = $this->createApplicationStage($applicationMetadata->applicationStageId, $identity, $subsidyStage);
        }

        return [$applicationStage, $subsidyStage];
    }

    /**
     * @throws FieldNotFoundException
     */
    private function loadField(SubsidyStage $subsidyStage, string $fieldCode): Field
    {
        $field = $this->formRepository->getField($subsidyStage, $fieldCode);
        if ($field === null) {
            throw new FieldNotFoundException(sprintf('Field with code "%s" not found for form with identifier "%s"!', $fieldCode, $subsidyStage->id));
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
    private function validateFileAnswer(ApplicationStage $applicationStage, ApplicationStageVersion $applicationStageVersion, Field $field): void
    {
        $answer = $this->applicationRepository->getAnswer($applicationStageVersion, $field);
        if ($answer === null) {
            throw new FileNotFoundException("Answer for file not found!");
        }

        $path = $this->getFilePath($applicationStage, $field);
        if (!$this->filesystemManager->disk(Disk::ApplicationFiles)->exists($path)) {
            throw new FileNotFoundException("File not found!");
        }
    }

    private function createOrUpdateAnswer(ApplicationStageVersion $applicationStageVersion, Field $field, mixed $value): void
    {
        $answer = $this->applicationRepository->makeAnswer($applicationStageVersion, $field);
        $answer->encrypted_answer = $this->encryptionService->encryptFieldValue(json_encode($value));
        $this->applicationRepository->saveAnswer($answer);
    }

    /**
     * @throws FileNotFoundException
     */
    private function processFieldValue(ApplicationStageVersion $applicationStageVersion, FieldValue $value): void
    {
        // answer for file already exists at this point
        if ($value->field->type !== FieldType::Upload) {
            $this->createOrUpdateAnswer($applicationStageVersion, $value->field, $value->value);
        }
    }

    private function processFieldValues(ApplicationStageVersion $applicationStageVersion, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->processFieldValue($applicationStageVersion, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValue(ApplicationStage $applicationStage, ApplicationStageVersion $applicationStageVersion, FieldValue $value): void
    {
        if ($value->field->type === FieldType::Upload) {
            $this->validateFileAnswer($applicationStage, $applicationStageVersion, $value->field);
        }

        // TODO: validate format of certain fields
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValues(ApplicationStage $applicationStage, ApplicationStageVersion $applicationStageVersion, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->validateFieldValue($applicationStage, $applicationStageVersion, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    public function processFormSubmit(FormSubmit $formSubmit): ApplicationStage
    {

        $applicationStage = DB::connection(Connection::APPLICATION)->transaction(function () use ($formSubmit) {


            [$applicationStage, $subsidyStage] = $this->loadOrCreateApplicationStageWithSubsidyStage($formSubmit->identity, $formSubmit->applicationMetadata);


            $json = $formSubmit->encryptedData; // TODO: decrypt
            $values = $this->decodingService->decodeFormValues($subsidyStage, $json);
            $applicationStageVersion = $this->createApplicationStageVersion($applicationStage);

            $this->validateFieldValues($applicationStage, $applicationStageVersion, $values);
            $this->processFieldValues($applicationStageVersion, $values);

            $applicationStage->status = ApplicationStageStatus::Submitted;
            $this->applicationRepository->saveApplicationStage($applicationStage);

            return $applicationStage;
        });

        assert($applicationStage instanceof ApplicationStage);
        return $applicationStage;
    }

    /**
     * @throws Throwable
     */
    private function doProcessFileUpload(FileUpload $fileUpload): void
    {
        [$applicationStage, $subsidyStage] = $this->loadOrCreateApplicationStageWithSubsidyStage($fileUpload->identity, $fileUpload->applicationMetadata);

        $field = $this->loadField($subsidyStage, $fileUpload->fieldCode);
        if ($field->type !== FieldType::Upload) {
            throw new FieldTypeMismatchException(sprintf('Field "%s" type mismatch, expected: %s, actual: %s', $field->code, FieldType::Upload->value, $field->type->value));
        }

        $decryptedContents = base64_decode($fileUpload->encryptedContents); // TODO: decrypt
        $size = strlen($decryptedContents);
        $encryptedContents = $decryptedContents; // TODO: encrypt based on application specific key

        $value = [
            'mimeType' => $fileUpload->mimeType,
            'extension' => $fileUpload->extension,
            'size' => $size
        ];

        $applicationStageVersion = $this->createApplicationStageVersion($applicationStage);


        $this->createOrUpdateAnswer(
            $applicationStageVersion,
            $field,
            $value
        );

        $path = $this->getFilePath($applicationStage, $field);
        $result = $this->filesystemManager->disk(Disk::ApplicationFiles)->put($path, $encryptedContents);
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
