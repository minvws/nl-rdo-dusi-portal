<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace App\Services;

use App\Models\Answer;
use App\Models\Application;
use App\Models\ApplicationStage;
use App\Models\ApplicationStageVersion;
use App\Models\Disk;
use App\Models\Enums\ApplicationStageVersionStatus;
use App\Repositories\ApplicationRepository;
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
use App\Models\Submission\FieldValue;
use App\Shared\Models\Connection;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Exception;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings("LongVariable")
 */

readonly class ApplicationService
{
    public function __construct(
        private SubsidyRepository     $subsidyRepository,
        private FormDecodingService   $decodingService,
        private EncryptionService     $encryptionService,
        private ApplicationRepository $appRepo,
        private FilesystemManager     $filesystemManager
    ) {
    }

    private function validateUuid(string $uuid): void
    {
        if (!Str::isUuid($uuid)) {
            throw new \InvalidArgumentException('Invalid UUID');
        }
    }

    private function loadApplicationStageIfExists(string $applicationStageId): ?ApplicationStage
    {
        $this->validateUuid($applicationStageId);
        return $this->appRepo->getApplicationStage($applicationStageId);
    }

    /**
     * @throws ApplicationMetadataMismatchException|ApplicationIdentityMismatchException
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
            throw new ApplicationIdentityMismatchException(
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
        $this->validateUuid($appMetadataId);
        $app = $this->createApplication($identity, $subsidyStage);
        $applicationStage = $this->appRepo->makeApplicationStage($app, $subsidyStage);
        $applicationStage->id = $appMetadataId;
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
            $latestApplicationStageVersion->answers(),
            function (Answer $answer) use ($applicationStageVersion) {
                $replicatedAnswer = $answer->replicate(['id']);
                $replicatedAnswer->ApplicationStageVersion()->associate($applicationStageVersion);
                $this->appRepo->saveAnswer($replicatedAnswer);
                return $replicatedAnswer;
            }
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

        if ($applicationStage instanceof ApplicationStage) {
            $this->validateIdentityAndApplicationMetadata($identity, $appMetadata, $applicationStage);
        } else {
            throw new Exception('ApplicationStage is not an instance of ApplicationStage');
        }


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
        ApplicationStageVersion $applicationStageVersion,
        Field $field
    ): void {
        Log::debug('Getting file for ', [
            $applicationStageVersion->id,
            $field->code,
        ]);
        $answer = $this->appRepo->getAnswer($applicationStageVersion, $field);
        if ($answer === null) {
            throw new FileNotFoundException("Answer for file {$field->code} not found!");
        }

        $path = $this->getFilePath($applicationStage, $field);
        if (!$this->filesystemManager->disk(Disk::APPLICATION_FILES)->exists($path)) {
            throw new FileNotFoundException("File not found!");
        }
    }

    private function createOrUpdateAnswer(
        ApplicationStageVersion $applicationStageVersion,
        Field $field,
        mixed $value
    ): void {
        $this->appRepo->createAnswer($applicationStageVersion, $field, $this->encryptionService
            ->encryptFieldValue(json_encode($value)));
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
    private function validateFieldValue(
        ApplicationStage $applicationStage,
        ApplicationStageVersion $applicationStageVersion,
        FieldValue $value
    ): void {
        if ($value->field->type === FieldType::Upload) {
            $this->validateFileAnswer($applicationStage, $applicationStageVersion, $value->field);
        }

        // TODO: validate format of certain fields
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValues(
        ApplicationStage $applicationStage,
        ApplicationStageVersion $applicationStageVersion,
        array $fieldValues
    ): void {
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

            [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
                $formSubmit->identity,
                $formSubmit->applicationMetadata
            );

            $json = $this->encryptionService->decryptFormSubmit($formSubmit->encryptedData);

            $values = $this->decodingService->decodeFormValues($subsidyStage, $json);
            $applicationStageVersion = $this->loadOrCreateApplicationStageVersion($applicationStage);

            $this->validateFieldValues($applicationStage, $applicationStageVersion, $values);
            $this->processFieldValues($applicationStageVersion, $values);

            $applicationStage->status = ApplicationStageVersionStatus::Submitted;
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
        [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
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

        $applicationStageVersion = $this->loadOrCreateApplicationStageVersion($applicationStage);

        [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
            $fileUpload->identity,
            $fileUpload->applicationMetadata
        );
        $applicationStageVersion = $this->createApplicationStageVersion($applicationStage);
        [$applicationStage, $subsidyStage] = $this->loadOrCreateAppStageWithSubsidyStage(
            $fileUpload->identity,
            $fileUpload->applicationMetadata
        );

        $this->createOrUpdateAnswer(
            $applicationStageVersion,
            $field,
            $value
        );

        $path = $this->getFilePath($applicationStage, $field);
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
}
