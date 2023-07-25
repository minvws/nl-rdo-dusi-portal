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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationService
{
    public function __construct(
        private FormRepository $formRepo,
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $appRepo,
        private FilesystemManager $filesystemManager
    ) {
    }

    private function loadApplicationStageIfExists(string $appStageId): ?ApplicationStage
    {
        return $this->appRepo->getApplicationStage($appStageId);
    }

    /**
     * @throws ApplicationMetadataMismatchException|ApplicationIdentityMismatchException
     */
    private function validateIdentityAndApplicationMetadata(
        Identity $identity,
        ApplicationMetadata $appMetadata,
        ApplicationStage $appStage
    ): void {
        if (
            $appStage->app->identity->type !== $identity->type ||
            $appStage->app->identity->identifier !== $identity->identifier
        ) {
            throw new ApplicationIdentityMismatchException(
                sprintf('Identity mismatch for app with identifier "%s"', $appStage->id)
            );
        }

        if ($appStage->subsidy_stage_id !== $appMetadata->subsidyStageId) {
            throw new ApplicationMetadataMismatchException(
                sprintf('Form mismatch for app with identifier "%s', $appStage->id)
            );
        }
    }

    /**
     * @throws FormNotFoundException
     */
    private function loadSubsidyStage(string $subsidyStageId): SubsidyStage
    {
        $subsidyStage = $this->formRepo->getSubsidyStage($subsidyStageId);
        if ($subsidyStage === null) {
            throw new FormNotFoundException(sprintf('Form with identifier "%s" does not exist!', $subsidyStageId));
        }
        return $subsidyStage;
    }

    private function createApplication(Identity $identity, SubsidyStage $subsidyStage): Application
    {
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
        $app = $this->createApplication($identity, $subsidyStage);
        $appStage = $this->appRepo->makeApplicationStage($app, $subsidyStage);
        $appStage->id = $appMetadataId;
        $appStage->status = ApplicationStageStatus::DRAFT;
        $appStage->user_id = Uuid::uuid4()->toString();
        $this->appRepo->saveApplicationStage($appStage);
        return $appStage;
    }

    private function createApplicationStageVersion(ApplicationStage $appStage): ApplicationStageVersion
    {
        $appStageVersion = $this->appRepo->makeApplicationStageVersion($appStage);
        $appStageVersion->version = 0;
        $this->appRepo->saveApplicationStageVersion($appStageVersion);
        return $appStageVersion;
    }

    /**
     * @throws Throwable
     */
    private function loadOrCreateAppStageWithSubsidyStage(Identity $identity, ApplicationMetadata $appMetadata): array
    {

        $appStage = $this->loadApplicationStageIfExists($appMetadata->applicationStageId);

        if ($appStage !== null) {
            $this->validateIdentityAndApplicationMetadata($identity, $appMetadata, $appStage);
        }

        $subsidyStage = $this->loadSubsidyStage($appMetadata->subsidyStageId);

        if ($appStage === null) {
            $appStage = $this->createApplicationStage($appMetadata->applicationStageId, $identity, $subsidyStage);
        }

        return [$appStage, $subsidyStage];
    }

    /**
     * @throws FieldNotFoundException
     */
    private function loadField(SubsidyStage $subsidyStage, string $fieldCode): Field
    {
        $field = $this->formRepo->getField($subsidyStage, $fieldCode);
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

    private function getFilePath(ApplicationStage $appStage, Field $field): string
    {
        return sprintf('%s/%s', $appStage->id, $field->code);
    }

    /**
     * @throws FileNotFoundException
     */
    private function validateFileAnswer(
        ApplicationStage $appStage,
        ApplicationStageVersion $appStageVersion,
        Field $field
    ): void {
        $answer = $this->appRepo->getAnswer($appStageVersion, $field);
        if ($answer === null) {
            throw new FileNotFoundException("Answer for file not found!");
        }

        $path = $this->getFilePath($appStage, $field);
        if (!$this->filesystemManager->disk(Disk::APPLICATIONFILES)->exists($path)) {
            throw new FileNotFoundException("File not found!");
        }
    }

    private function createOrUpdateAnswer(ApplicationStageVersion $appStageVersion, Field $field, mixed $value): void
    {
        $answer = $this->appRepo->makeAnswer($appStageVersion, $field);
        $answer->encrypted_answer = $this->encryptionService->encryptFieldValue(json_encode($value));
        $this->appRepo->saveAnswer($answer);
    }

    /**
     * @throws FileNotFoundException
     */
    private function processFieldValue(ApplicationStageVersion $appStageVersion, FieldValue $value): void
    {
        // answer for file already exists at this point
        if ($value->field->type !== FieldType::Upload) {
            $this->createOrUpdateAnswer($appStageVersion, $value->field, $value->value);
        }
    }

    private function processFieldValues(ApplicationStageVersion $appStageVersion, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->processFieldValue($appStageVersion, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValue(
        ApplicationStage $appStage,
        ApplicationStageVersion $appStageVersion,
        FieldValue $value
    ): void {
        if ($value->field->type === FieldType::Upload) {
            $this->validateFileAnswer($appStage, $appStageVersion, $value->field);
        }

        // TODO: validate format of certain fields
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValues(
        ApplicationStage $appStage,
        ApplicationStageVersion $appStageVersion,
        array $fieldValues
    ): void {
        foreach ($fieldValues as $fieldValue) {
            $this->validateFieldValue($appStage, $appStageVersion, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    public function processFormSubmit(FormSubmit $formSubmit): ApplicationStage
    {

        $appStage = DB::connection(Connection::APPLICATION)->transaction(function () use ($formSubmit) {


            [$appStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
                $formSubmit->identity,
                $formSubmit->applicationMetadata
            );


            $json = $formSubmit->encryptedData; // TODO: decrypt
            $values = $this->decodingService->decodeFormValues($subsidyStage, $json);
            $appStageVersion = $this->createApplicationStageVersion($appStage);

            $this->validateFieldValues($appStage, $appStageVersion, $values);
            $this->processFieldValues($appStageVersion, $values);

            $appStage->status = ApplicationStageStatus::Submitted;
            $this->appRepo->saveApplicationStage($appStage);

            return $appStage;
        });

        assert($appStage instanceof ApplicationStage);
        return $appStage;
    }

    /**
     * @throws Throwable
     */
    private function doProcessFileUpload(FileUpload $fileUpload): void
    {
        [$appStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
            $fileUpload->identity,
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

        $decryptedContents = base64_decode($fileUpload->encryptedContents); // TODO: decrypt
        $size = strlen($decryptedContents);
        $encryptedContents = $decryptedContents; // TODO: encrypt based on app specific key

        $value = [
            'mimeType' => $fileUpload->mimeType,
            'extension' => $fileUpload->extension,
            'size' => $size
        ];

        $appStageVersion = $this->createApplicationStageVersion($appStage);


        $this->createOrUpdateAnswer(
            $appStageVersion,
            $field,
            $value
        );

        $path = $this->getFilePath($appStage, $field);
        $result = $this->filesystemManager->disk(Disk::APPLICATIONFILES)->put($path, $encryptedContents);
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
