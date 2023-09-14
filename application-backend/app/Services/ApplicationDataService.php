<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Exception;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\File;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use stdClass;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationDataService
{
    private JSONEncoder $encoder;
    private JSONDecoder $decoder;

    public function __construct(
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository,
        private ApplicationFileRepository $applicationFileRepository
    ) {
        $this->encoder = new JSONEncoder();
        $this->decoder = new JSONDecoder();
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

    private function saveFieldValue(ApplicationStage $applicationStage, FieldValue $fieldValue): void
    {
        $answer = $this->applicationRepository->makeAnswer($applicationStage, $fieldValue->field);
        $this->cleanUpUnusedFiles($applicationStage, $fieldValue);
        $json = $this->encoder->encode($fieldValue->value);
        $answer->encrypted_answer = $this->encryptionService->encryptData($json);
        $this->applicationRepository->saveAnswer($answer);
    }

    public function saveApplicationData(ApplicationStage $applicationStage, object|string $data): void
    {
        $fieldValues = $this->decodingService->decodeFormValues($applicationStage->subsidyStage, $data);

        // TODO: RickL Validation will be in other PR

        foreach ($fieldValues as $fieldValue) {
            $this->saveFieldValue($applicationStage, $fieldValue);
        }
    }

    /**
     * @throws Exception
     */
    public function getApplicationData(Application $application): object
    {
        $answers = $this->applicationRepository->getApplicationStageAnswersByStageNumber($application, 1);

        $data = new stdClass();
        foreach ($answers as $answer) {
            $json = $this->encryptionService->decryptBase64EncodedData($answer->encrypted_answer);
            $value = $this->decoder->decode($json)->decodeIfPresent();
            if ($value !== null) {
                $data->{$answer->field->code} = $value;
            }
        }

        return $data;
    }
}
