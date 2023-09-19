<?php

/**
 * Application Data Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Exception;
use Illuminate\Contracts\Encryption\Encrypter;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\File;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationEncryptionService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use stdClass;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationDataService
{
    public function __construct(
        private FormDecodingService $decodingService,
        private ApplicationEncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository,
        private ApplicationFileRepository $applicationFileRepository,
        private JSONEncoder $jsonEncoder,
        private JSONDecoder $jsonDecoder,
    ) {
    }

    private function cleanUpUnusedFiles(ApplicationStage $applicationStage, FieldValue $value): void
    {
        if ($value->field->type !== FieldType::Upload) {
            return;
        }

        $usedIds = [];
        if ($value->value instanceof FileList) {
            $usedIds = array_map(fn (File $file) => $file->id, $value->value->items);
        }

        // TODO:
        // At this time the values should have been validated and that also means that the file really
        // needs to exist for a certain ID. But as the validation isn't finished yet, we validate here
        // for now.
        foreach ($usedIds as $id) {
            if (!$this->applicationFileRepository->fileExists($applicationStage, $value->field, $id)) {
                throw new Exception('File not found!');
            }
        }

        // TODO: what if the transaction fails and we already deleted the files? maybe only do after submit?!
        $this->applicationFileRepository->unlinkUnusedFiles($applicationStage, $value->field, $usedIds);
    }

    private function saveFieldValue(
        Encrypter $encrypter,
        ApplicationStage $applicationStage,
        FieldValue $fieldValue
    ): void {
        $this->cleanUpUnusedFiles($applicationStage, $fieldValue);
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
     */
    public function saveApplicationData(
        ApplicationStage $applicationStage,
        object $data
    ): void {
        // Remove all answers for this stage because we received new data
        $this->applicationRepository->deleteAnswersByStage($applicationStage);

        // New encryption key for each save, so we do not reuse the same key
        [$encryptedKey, $encrypter] = $this->encryptionService->generateEncryptionKey();
        $applicationStage->encrypted_key = $encryptedKey;

        // Decode received form data
        $fieldValues = $this->decodingService->decodeFormValues($applicationStage->subsidyStage, $data);

        // TODO: RickL Validation will be in other PR

        foreach ($fieldValues as $fieldValue) {
            $this->saveFieldValue($encrypter, $applicationStage, $fieldValue);
        }
    }

    /**
     * @throws Exception
     */
    public function getApplicationData(Application $application): object
    {
        $applicationStage = $this->applicationRepository->getApplicationStageByStageNumber($application, 1, true);
        if ($applicationStage === null) {
            return new stdClass();
        }

        $encrypter = $this->encryptionService->getEncrypter($applicationStage);

        $data = new stdClass();
        foreach ($applicationStage->answers as $answer) {
            $value = $encrypter->decrypt($answer->encrypted_answer);
            if ($value === null) {
                continue;
            }

            $value = match ($answer->field->type) {
                FieldType::Upload => $this->jsonDecoder->decode($value)->decodeObject(FileList::class),
                default => $value,
            };

            $data->{$answer->field->code} = $value;
        }

        return $data;
    }
}
