<?php

/**
 * Application File Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use finfo;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Application\Backend\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\DTO\TemporaryFile;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\FileValidator;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUploadResult;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationFileService
{
    use LoadIdentity;
    use LoadApplication;

    public function __construct(
        private FrontendDecryption $frontendDecryptionService,
        private ResponseEncryptionService $responseEncryptionService,
        private IdentityService $identityService,
        private ApplicationRepository $applicationRepository,
        private ApplicationFileManager $applicationFileManager,
        private SubsidyRepository $subsidyRepository,
        private LoggerInterface $logger,
        private FileValidator $fileValidator,
        private EncryptedResponseExceptionHelper $exceptionHelper
    ) {
    }

    private function loadApplicationStage(Application $application): ApplicationStage
    {
        if (!$application->status->isEditableForApplicant()) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly'
            );
        }

        $applicationStage = $application->currentApplicationStage;
        if ($applicationStage === null || $applicationStage->subsidyStage->stage !== 1) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly'
            );
        }

        return $applicationStage;
    }

    private function loadField(ApplicationStage $applicationStage, string $fieldCode): Field
    {
        $field = $this->subsidyRepository->getFieldForSubsidyStageAndCode(
            $applicationStage->subsidyStage,
            $fieldCode
        );

        if ($field === null || $field->type !== FieldType::Upload) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::BAD_REQUEST,
                'field_not_found'
            );
        }

        return $field;
    }

    private function doSaveApplicationFile(EncryptedApplicationFileUploadParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);
        $application = $this->loadApplication($identity, $params->applicationReference);
        $applicationStage = $this->loadApplicationStage($application);
        $field = $this->loadField($applicationStage, $params->fieldCode);

        $id = Uuid::uuid4()->toString();

        $decryptedContent = $this->frontendDecryptionService->decrypt($params->data);

        $tempFile = new TemporaryFile($decryptedContent);
        $tempFile->makeGroupReadable();

        $validator = $this->fileValidator->getValidator($field, $tempFile->getUploadedFile());
        if ($validator->fails()) {
            $this->logger->debug('File validation failed', [
                'errors' => $validator->errors()->toArray(),
                'file' => [
                    'size' => $tempFile->getUploadedFile()->getSize(),
                    'mimeType' => $tempFile->getUploadedFile()->getMimeType()
                ]
            ]);

            // After calling fails, the validator has run and the file can be closed
            $tempFile->close();

            throw new EncryptedResponseException(
                EncryptedResponseStatus::BAD_REQUEST,
                'file_validation_failed'
            );
        }

        $tempFile->close();

        $this->applicationFileManager->writeFile($applicationStage, $field, $id, $decryptedContent);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::CREATED,
            new FileUploadResult($id),
            $params->publicKey
        );
    }

    public function saveApplicationFile(EncryptedApplicationFileUploadParams $params): EncryptedResponse
    {
        try {
            return DB::transaction(fn () => $this->doSaveApplicationFile($params));
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::UPLOAD_APPLICATION_FILE,
                $params->publicKey
            );
        }
    }

    private function doGetApplicationFile(ApplicationFileParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);
        $application = $this->loadApplication($identity, $params->applicationReference);

        $applicationStage = $this->applicationRepository->getApplicantApplicationStage($application, true);
        assert($applicationStage !== null);

        $field = $this->loadField($applicationStage, $params->fieldCode);
        if (!$this->applicationFileManager->fileExists($applicationStage, $field, $params->id)) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'file_not_found'
            );
        }

        $content = $this->applicationFileManager->readFile($applicationStage, $field, $params->id);

        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $contentType = $fileInfo->buffer($content) ?: 'application/octet-stream';

        return $this->responseEncryptionService->encrypt(
            EncryptedResponseStatus::OK,
            $content,
            $contentType,
            $params->publicKey
        );
    }

    public function getApplicationFile(ApplicationFileParams $params): EncryptedResponse
    {
        try {
            return $this->doGetApplicationFile($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_APPLICATION_FILE,
                $params->publicKey
            );
        }
    }
}
