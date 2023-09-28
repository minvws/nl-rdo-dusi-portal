<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Http\Response;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;

class ApplicationFileService
{
    public function __construct(
        protected ApplicationFileManager $applicationFileManager,
        protected ApplicationStageEncryptionService $applicationStageEncryptionService
    ) {
    }

    public function getApplicationFile(
        Application $application,
        string $applicationStageId,
        string $fieldCode,
        string $fileId
    ): Response {
        //TODO GB: Test this service
        $applicationStage = $application->applicationStages()->findOrFail($applicationStageId);
        assert($applicationStage instanceof ApplicationStage);

        $field = $applicationStage->subsidyStage->fields->where('code', $fieldCode)->firstOrFail();
        $file = $this->applicationFileManager->readFile(
            $applicationStage,
            $field,
            $fileId
        );
        $answer = $applicationStage->answers->where('field_id', $field->id)->firstOrFail();

        $encrypter = $this->applicationStageEncryptionService->getEncrypter($applicationStage);
        $decrypted = json_decode($encrypter->decrypt($answer->encrypted_answer));

        $response = response(
            $file,
            200,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $decrypted[0]->name . '"',
            ]
        );
        assert($response instanceof Response);
        return $response;
    }
}
