<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Exception;
use Illuminate\Http\Response;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApplicationFileService
{
    public function __construct(
        protected ApplicationFileManager $applicationFileManager,
        protected ApplicationStageEncryptionService $applicationStageEncryptionService,
    ) {
    }

    public function getApplicationFile(
        ApplicationStage $applicationStage,
        Field $field,
        string $fileId
    ): Response {
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

    public function createApplicationFile(
        ApplicationStage $applicationStage,
        Field $field,
        UploadedFile $file
    ): string {
        $fileId = Uuid::uuid4()->toString();
        $this->applicationFileManager->writeFile(
            $applicationStage,
            $field,
            $fileId,
            $file->getContent(),
        );

        return $fileId;
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
