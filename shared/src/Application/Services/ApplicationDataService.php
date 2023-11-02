<?php // phpcs:disable PSR1.Files.SideEffects


/**
 * Application Data Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Exception;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageData;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FieldValidationResponse;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use stdClass;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @throws ValidationException
     */
    public function saveApplicationStageData(
        ApplicationStage $applicationStage,
        object $data,
        bool $submit
    ): void {
        // Remove all answers for this stage because we received new data
        $this->applicationRepository->deleteAnswersByStage($applicationStage);

        // New encryption key for each save, so we do not reuse the same key
        [$encryptedKey, $encrypter] = $this->encryptionService->generateEncryptionKey();
        $applicationStage->encrypted_key = $encryptedKey;
        $applicationStage->save();

        // Decode received form data
        $fieldValues = $this->decodingService->decodeFormValues($applicationStage->subsidyStage, $data);

        // Validate, throws a ValidationException on error
        $validator = $this->validationService->getValidator($applicationStage, $fieldValues, $submit);
        $validator->validate();

        foreach ($fieldValues as $fieldValue) {
            $this->saveFieldValue($encrypter, $applicationStage, $fieldValue);
        }
    }

    public function validateFieldValues(
        ApplicationStage $applicationStage,
        object $data,
        bool $submit
    ): FieldValidationResponse {
        // Decode received form data
        $fieldValues = $this->decodingService->decodeFormValues($applicationStage->subsidyStage, $data);
        $validator = $this->validationService->getValidator($applicationStage, $fieldValues, $submit);

        $validationResult = $validator->validate();

        return new FieldValidationResponse(
            validationResult: $validationResult,
        );
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
     * @param ApplicationStage $applicationStage
     *
     * @return array<int, ApplicationStageData>
     */
    public function getApplicationStageDataUpToIncluding(ApplicationStage $applicationStage): array
    {
        $answersByStage = $this->applicationRepository->getAnswersForApplicationStagesUpToIncluding($applicationStage);

        $result = [];
        foreach ($answersByStage->stages as $stageAnswers) {
            $result[$stageAnswers->stage->subsidyStage->stage] =
                $this->mapAnswersToData($stageAnswers->stage, $stageAnswers->answers);
        }

        return $result;
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
}
