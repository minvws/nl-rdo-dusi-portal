<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Exception;
use Illuminate\Http\Response;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;

class ApplicationFileService
{
    public function __construct(
        protected ApplicationFileManager $applicationFileManager,
        protected ApplicationStageEncryptionService $applicationStageEncryptionService,
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

        return new Response(
            $file,
            200,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $decrypted[0]->name . '"',
            ]
        );
    }

    public function writeApplicationFile(
        Application $application,
        string $applicationStageId,
        string $fieldCode,
        string $fileId,
        string $content
    ): Response {
        //TODO GB: Test this service
        $applicationStage = $application->applicationStages()->findOrFail($applicationStageId);
        assert($applicationStage instanceof ApplicationStage);

        $field = $applicationStage->subsidyStage->fields->where('code', $fieldCode)->firstOrFail();
        $this->applicationFileManager->writeFile(
            $applicationStage,
            $field,
            $fileId,
            $content
        );

        return new Response(
            status: 201,
        );
    }

    public function getMessageFile(ApplicationMessage $message, MessageDownloadFormat $format): Response
    {
        $content = match ($format) {
            MessageDownloadFormat::HTML => $this->applicationFileManager->readEncryptedFile($message->html_path),
            MessageDownloadFormat::PDF => $this->applicationFileManager->readEncryptedFile($message->pdf_path),
        };

        if (empty($content)) {
            throw new Exception('Empty content');
        }

        $contentType = match ($format) {
            MessageDownloadFormat::HTML => 'text/html',
            MessageDownloadFormat::PDF => 'application/pdf'
        };

        $fileExtension = match ($format) {
            MessageDownloadFormat::HTML => 'html',
            MessageDownloadFormat::PDF => 'pdf'
        };

        $fileName = strtolower(str_replace(' ', '-', $message->subject));

        return new Response(
            $content,
            200,
            [
                'Content-Type' => $contentType,
                'Content-Disposition' => sprintf('inline; filename="%s.%s"', $fileName, $fileExtension),
            ]
        );
    }
}
