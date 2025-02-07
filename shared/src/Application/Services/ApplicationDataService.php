<?php

/**
 * Application Data Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Exception;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Collection;
use League\CommonMark\Exception\LogicException;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageData;
use MinVWS\DUSi\Shared\Application\Enums\ApplicationStageGrouping;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ValidationErrorException;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\FieldReference;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHashField;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use RuntimeException;
use stdClass;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
readonly class ApplicationDataService
{
    public function __construct(
        private FormDecodingService $decodingService,
        private ApplicationStageEncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository,
        private ValidationService $validationService,
        private ApplicationFileManager $applicationFileManager,
        private JSONEncoder $jsonEncoder,
        private JSONDecoder $jsonDecoder,
        private SubsidyStashFieldHasher $subsidyStashFieldHasher,
        private ApplicationFieldHookService $applicationFieldHookService,
        private SubsidyRepository $subsidyRepository,
    ) {
    }

    private function saveFieldValue(
        Encrypter $encrypter,
        ApplicationStage $applicationStage,
        FieldValue $fieldValue
    ): void {
        $this->applicationFileManager->cleanUpUnusedFiles($applicationStage, $fieldValue);
        if ($fieldValue->value === null) {
            // Do not create an answer if the value is null
            // We should not encrypt null values
            return;
        }

        $answer = $this->applicationRepository->makeAnswer($applicationStage, $fieldValue->field);

        $value = match ($fieldValue->field->type) {
            FieldType::Upload => $this->jsonEncoder->encode($fieldValue->value),
            default => $fieldValue->value,
        };
        $answer->encrypted_answer = $encrypter->encrypt($value);

        $this->applicationRepository->saveAnswer($answer);
    }

    /**
     * @throws Throwable
     * @throws ValidationErrorException
     */
    public function saveApplicationStageData(
        ApplicationStage $applicationStage,
        object $data,
        bool $submit
    ): array {
        // Decode received form data
        $fieldValues = $this->decodingService->decodeFormValues($applicationStage->subsidyStage, $data);

        $fieldValues = $this->applicationFieldHookService->findAndExecuteHooks($fieldValues, $applicationStage);

        // Validate, throws a ValidationException on error
        $validationResult = $this->validateFieldValues($applicationStage, $fieldValues, $submit);

        $this->updateAnswersForApplicationStage($applicationStage, $fieldValues);

        return $validationResult;
    }

    private function saveFieldValues(array $fieldValues, Encrypter $encrypter, ApplicationStage $applicationStage): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->saveFieldValue($encrypter, $applicationStage, $fieldValue);
        }
    }

    public function validateFieldValues(
        ApplicationStage $applicationStage,
        array $fieldValues,
        bool $submit
    ): array {
        // Decode received form data
        $validator = $this->validationService->getValidator($applicationStage, $fieldValues, $submit);

        return $validator->validate();
    }

    /**
     * @throws Exception
     */
    public function getApplicationStageData(ApplicationStage $applicationStage): object
    {
        return $this->mapAnswersToData($applicationStage, $applicationStage->answers->all())->data;
    }


    /**
     * @return array<string, FieldValue>
     * @throws Exception
     */
    public function getApplicationStageDataAsFieldValues(ApplicationStage $applicationStage): array
    {
        $encrypter = $this->encryptionService->getEncrypter($applicationStage);

        $result = [];
        foreach ($applicationStage->answers as $answer) {
            $value = $this->mapAnswerToValue($answer, $encrypter);
            if ($value !== null) {
                $result[$answer->field->code] = new FieldValue($answer->field, $value);
            }
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function getApplicationStageDataForField(
        ApplicationStage $applicationStage,
        Field $field
    ): FileList|string|int|bool|float|array|null {
        $answer = $this->applicationRepository->getApplicationStageAnswerForField($applicationStage, $field);
        if ($answer === null) {
            return null;
        }

        $encrypter = $this->encryptionService->getEncrypter($applicationStage);

        return $this->mapAnswerToValue($answer, $encrypter);
    }

    /**
     * @throws Exception
     */
    public function getApplicationStageDataForFieldByFieldReference(
        Application $application,
        FieldReference $field,
    ): FileList|string|int|bool|float|array|null {
        // Retrieve the subsidy stage and field
        $subsidyStage = $application->subsidyVersion->subsidyStages->sole('stage', $field->stage);
        $field = $subsidyStage->fields->sole('code', $field->fieldCode);

        $applicationStage = $this->applicationRepository->getLatestSubmittedOrCurrentApplicationStageForSubsidyStage(
            application: $application,
            subsidyStage: $subsidyStage,
        );
        if ($applicationStage === null) {
            throw new RuntimeException('Application data requested for field without an application stage');
        }

        return $this->getApplicationStageDataForField($applicationStage, $field);
    }

    /**
     * @param ApplicationStage $applicationStage
     *
     * @return array<int, ApplicationStageData>
     */
    public function getApplicationStageDataUniqueByStageUpToIncluding(
        ApplicationStage $applicationStage,
        bool $readOnly = false
    ): array {
        return $this->getApplicationStageDataUpToIncluding(
            $applicationStage,
            ApplicationStageGrouping::ByStageNumber,
            $readOnly
        );
    }

    /**
     * @param ApplicationStage $applicationStage
     *
     * @return array<int, ApplicationStageData>
     */
    public function getApplicationStageDataUniqueBySequenceUpToIncluding(
        ApplicationStage $applicationStage,
        bool $readOnly = false
    ): array {
        return $this->getApplicationStageDataUpToIncluding(
            $applicationStage,
            ApplicationStageGrouping::BySequenceNumber,
            $readOnly
        );
    }

    /**
     * @param ApplicationStage $applicationStage
     * @param ApplicationStageGrouping $applicationStageGrouping
     *
     * @return array<int, ApplicationStageData>
     */
    private function getApplicationStageDataUpToIncluding(
        ApplicationStage $applicationStage,
        ApplicationStageGrouping $applicationStageGrouping,
        bool $readOnly = false
    ): array {
        $answersByGrouping = $this->applicationRepository
            ->getAnswersForApplicationStagesUpToIncluding($applicationStage, $applicationStageGrouping, $readOnly);
        $result = [];
        foreach ($answersByGrouping->stages as $stageAnswers) {
            $groupingKey = match ($applicationStageGrouping) {
                ApplicationStageGrouping::ByStageNumber=> $stageAnswers->stage->subsidyStage->stage,
                ApplicationStageGrouping::BySequenceNumber=> $stageAnswers->stage->sequence_number
            };

            $result[$groupingKey] =
                $this->mapAnswersToData($stageAnswers->stage, $stageAnswers->answers);
        }
        return $result;
    }

    public function getApplicantApplicationStageData(Application $application): ?object
    {
        $applicantApplicationStage = $this->applicationRepository->getCurrentApplicantApplicationStage(
            $application,
            true
        );

        if ($applicantApplicationStage === null) {
            return null;
        }

        return $this->getApplicationStageData($applicantApplicationStage);
    }

    /**
     * @param array<Answer> $answers
     */
    private function mapAnswersToData(ApplicationStage $stage, array $answers): ApplicationStageData
    {
        $encrypter = $this->encryptionService->getEncrypter($stage);

        $data = new stdClass();
        foreach ($answers as $answer) {
            $value = $this->mapAnswerToValue($answer, $encrypter);
            if ($value !== null) {
                $data->{$answer->field->code} = $value;
            }
        }

        return new ApplicationStageData($stage, $data);
    }

    private function mapAnswerToValue(Answer $answer, Encrypter $encrypter): FileList|string|int|bool|float|array|null
    {
        $value = $answer->encrypted_answer !== null ? $encrypter->decrypt($answer->encrypted_answer) : null;
        if ($value === null) {
            return null;
        }

        return match ($answer->field->type) {
            FieldType::Upload => $this->jsonDecoder->decode($value)->decodeObject(FileList::class),
            default => $value,
        };
    }

    private function updateSubsidyStageHashes(array $fieldValues, ApplicationStage $applicationStage): void
    {
        $fieldValues = array_filter($fieldValues, fn($fieldValue) => $fieldValue->valueToString() !== "");
        foreach ($applicationStage->subsidyStage->subsidyStageHashes as $subsidyStageHash) {
            $this->updateSubsidyStageHash($fieldValues, $subsidyStageHash, $applicationStage);
        }
    }

    public function updateSubsidyStageHash(
        array $fieldValues,
        SubsidyStageHash $subsidyStageHash,
        ApplicationStage $applicationStage
    ): void {
        /**
         * @var Collection|SubsidyStageHashField[] $subsidyStageHashFields
         */
        $subsidyStageHashFields = $subsidyStageHash->subsidyStageHashFields()->get();

        if ($this->fieldValuesContainsSubsidyStageHashField($fieldValues, $subsidyStageHashFields)) {
            $this->updateOrNewApplicationStageFieldHash(
                $subsidyStageHash,
                $applicationStage,
                $fieldValues
            );
        }
    }

    private function updateOrNewApplicationStageFieldHash(
        SubsidyStageHash $subsidyStageHash,
        ApplicationStage $applicationStage,
        array $fieldValues
    ): void {
        $hash = $this->makeApplicationFieldHash($subsidyStageHash, $fieldValues, $applicationStage);
        $this->applicationRepository->updateOrNewApplicationStageFieldHash(
            $subsidyStageHash,
            $applicationStage->application,
            $hash
        );
    }

    private function makeApplicationFieldHash(
        SubsidyStageHash $subsidyStageHash,
        array $fieldValues,
        ApplicationStage $applicationStage
    ): string {
        return $this->subsidyStashFieldHasher->makeApplicationFieldHash(
            $subsidyStageHash,
            $fieldValues,
            $applicationStage
        );
    }

    public function fieldValuesContainsSubsidyStageHashField(
        array $fieldValues,
        Collection $subsidyStageHashFields
    ): bool {
        /**
         * @var Collection $hashFieldCodesMap
         */
        $hashFieldCodesMap = $subsidyStageHashFields->map(
            fn(SubsidyStageHashField $field) => $field->field?->code
        );

        foreach ($fieldValues as $fieldValue) {
            if ($hashFieldCodesMap->contains($fieldValue->field->code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ApplicationStage $applicationStage
     * @param array $fieldValues
     * @return void
     * @throws Exception
     */
    private function updateAnswersForApplicationStage(ApplicationStage $applicationStage, array $fieldValues): void
    {
        // Remove all answers for this stage because we received new data
        $this->applicationRepository->deleteAnswersByStage($applicationStage);

        // New encryption key for each save, so we do not reuse the same key
        [$encryptedKey, $encrypter] = $this->encryptionService->generateEncryptionKey();
        $applicationStage->encrypted_key = $encryptedKey;
        $applicationStage->save();

        $this->saveFieldValues($fieldValues, $encrypter, $applicationStage);

        $this->updateSubsidyStageHashes($fieldValues, $applicationStage);
    }

    public function decryptForApplicantStage(
        Application $application,
        string $encryptedValue
    ): string {
        $applicantApplicationStage =
            $this->applicationRepository
                ->getCurrentApplicantApplicationStage($application, false);

        if (is_null($applicantApplicationStage)) {
            throw new LogicException(sprintf('No Applicant stage found for Application: %s', $application->id));
        }

        $encrypter = $this->encryptionService->getEncrypter($applicantApplicationStage);

        return $encrypter->decrypt($encryptedValue);
    }

    public function encryptForStage(
        ApplicationStage $applicationStage,
        string $value
    ): string {
        $encrypter = $this->encryptionService->getEncrypter($applicationStage);

        return $encrypter->encrypt($value);
    }

    public function calculateCalculatedFieldsForNewApplicationStage(ApplicationStage $stage): void
    {
        if ($stage->answers()->count() > 0) {
            return;
        }

        $defaultFieldValues = $this->getCalculatedFieldsDefaultValue($stage);
        if (count($defaultFieldValues) === 0) {
            return;
        }

        $fieldValues = $this->applicationFieldHookService->findAndExecuteHooks($defaultFieldValues, $stage);

        $this->updateAnswersForApplicationStage($stage, $fieldValues);
    }

    /**
     * @param ApplicationStage $stage
     * @return FieldValue[]
     */
    private function getCalculatedFieldsDefaultValue(ApplicationStage $stage): array
    {
        $fields = $this->subsidyRepository->getCalculatedFields($stage->subsidyStage);

        return $fields->map(function (Field $field) {
            return new FieldValue($field, null);
        })->toArray();
    }
}
