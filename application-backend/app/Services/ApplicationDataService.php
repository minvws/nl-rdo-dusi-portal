<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Exception;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFile;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use stdClass;

readonly class ApplicationDataService
{
    public function __construct(
        private EncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function getApplicationData(Application $application): object
    {
        $answers = $this->applicationRepository->getApplicationStageAnswersByStageNumber($application, 1);

        $data = new stdClass();
        foreach ($answers as $answer) {
            if ($answer->field->type === FieldType::Upload) {
                continue; // uploads are not part of the returned data
            }

            $decryptedValue = $this->encryptionService->decryptBase64EncodedData($answer->encrypted_answer);
            $decodedValue = json_decode($decryptedValue, flags: JSON_THROW_ON_ERROR);
            $data->{$answer->field->code} = $decodedValue;
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function getApplicationFiles(Application $application): array
    {
        $files = [];

        $answers = $this->applicationRepository->getApplicationStageAnswersByStageNumber($application, 1);
        foreach ($answers as $answer) {
            if ($answer->field->type !== FieldType::Upload) {
                continue;
            }

            $decryptedValue = $this->encryptionService->decryptBase64EncodedData($answer->encrypted_answer);
            $decodedValue = json_decode($decryptedValue, flags: JSON_THROW_ON_ERROR);

            // TODO: retrieving file info should be more formalized
            // TODO: we currently fake the id and originalName, make sure we create/store these on upload!

            if (!isset($decodedValue->mimeType) || !isset($decodedValue->extension) || !isset($decodedValue->size)) {
                continue;
            }

            $files[] = new ApplicationFile(
                id: $answer->field->code,
                fieldCode: $answer->field->code,
                originalName: $answer->field->code . '.' . $decodedValue->extension,
                mimeType: $decodedValue->mimeType,
                size: $decodedValue->size
            );
        }

        return $files;
    }
}
