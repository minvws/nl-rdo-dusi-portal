<?php

/**
 * Application File Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use finfo;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Application\Backend\Services\Traits\HandleException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Application\Backend\Services\Validation\FileValidator;
use MinVWS\DUSi\Shared\Application\DTO\TemporaryFile;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileRepositoryService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUploadResult;
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
    use HandleException;
    use LoadIdentity;
    use LoadApplication;

    public function __construct(
        private ResponseEncryptionService $responseEncryptionService,
        private IdentityService $identityService,
        private ApplicationRepository $applicationRepository,
        private ApplicationFileRepositoryService $applicationFileRepository,
        private SubsidyRepository $subsidyRepository,
        private LoggerInterface $logger,
        private FileValidator $fileValidator,
    ) {
    }

    private function loadApplicationStage(Application $application): ApplicationStage
    {
        if (!$application->status->isEditableForApplicant()) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly',
                'Application is read-only'
            );
        }

        $applicationStage = $application->currentApplicationStage;
        if ($applicationStage->subsidyStage->stage !== 1) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly',
                'Application is read-only'
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
                EncryptedResponseStatus::NOT_FOUND,
                'field_not_found',
                'File field with the given code does not exist for this application stage'
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

        $decryptedContent = $params->data->data; // TODO: $this->decryptionService->decrypt($params->data);

        $tempFile = new TemporaryFile($decryptedContent);
        $tempFile->makeGroupReadable();

        $validator = $this->fileValidator->getValidator($field, $tempFile->getUploadedFile());
        if ($validator->fails()) {
            // After calling fails, the validator has run and the file can be closed
            $tempFile->close();

            return $this->responseEncryptionService->encryptCodable(
                EncryptedResponseStatus::BAD_REQUEST,
                new Error('file_validation_failed', 'File validation failed.'),
                $params->publicKey,
            );
        }

        $tempFile->close();

        $this->applicationFileRepository->writeFile($applicationStage, $field, $id, $decryptedContent);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::CREATED,
            new FileUploadResult($id),
            $params->publicKey
        );
    }

    public function saveApplicationFile(EncryptedApplicationFileUploadParams $params): EncryptedResponse
    {
        return DB::transaction(function () use ($params) {
            try {
                return $this->doSaveApplicationFile($params);
            } catch (EncryptedResponseException $e) {
                return $this->responseEncryptionService->encryptCodable(
                    $e->getStatus(),
                    $e->getError(),
                    $params->publicKey
                );
            }
        });
    }

    public function getApplicationFile(ApplicationFileParams $params): EncryptedResponse
    {
        try {
            $identity = $this->loadIdentity($params->identity);
            $application = $this->loadApplication($identity, $params->applicationReference);

            $applicationStage = $this->applicationRepository->getApplicantApplicationStage($application, true);
            assert($applicationStage !== null);

            $field = $this->loadField($applicationStage, $params->fieldCode);
            if (!$this->applicationFileRepository->fileExists($applicationStage, $field, $params->id)) {
                throw new EncryptedResponseException(
                    EncryptedResponseStatus::NOT_FOUND,
                    'file_not_found',
                    'File does not exist'
                );
            }

            $content = $this->applicationFileRepository->readFile($applicationStage, $field, $params->id);

            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $contentType = $fileInfo->buffer($content) ?: 'application/octet-stream';

            return $this->responseEncryptionService->encrypt(
                EncryptedResponseStatus::OK,
                $content,
                $contentType,
                $params->publicKey
            );
        } catch (Throwable $e) {
            return $this->handleException(__METHOD__, $e, $params->publicKey);
        }
    }
}
