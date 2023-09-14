<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use finfo;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\Traits\HandleException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
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
        private EncryptionService $encryptionService,
        private IdentityService $identityService,
        private ApplicationRepository $applicationRepository,
        private ApplicationFileRepository $applicationFileRepository,
        private SubsidyRepository $subsidyRepository,
        private LoggerInterface $logger
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
        $encryptedContent = $this->encryptionService->encryptData($decryptedContent);
        $this->applicationFileRepository->writeFile($applicationStage, $field, $id, $encryptedContent);

        return $this->encryptionService->encryptCodableResponse(
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
                return $this->encryptionService->encryptCodableResponse(
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

            $applicationStage = $this->applicationRepository->getApplicationStageByStageNumber($application, 1);
            assert($applicationStage !== null);

            $field = $this->loadField($applicationStage, $params->fieldCode);
            if (!$this->applicationFileRepository->fileExists($applicationStage, $field, $params->id)) {
                throw new EncryptedResponseException(
                    EncryptedResponseStatus::NOT_FOUND,
                    'file_not_found',
                    'File does not exist'
                );
            }

            $encryptedContent = $this->applicationFileRepository->readFile($applicationStage, $field, $params->id);
            assert($encryptedContent !== null);
            $content = $this->encryptionService->decryptData($encryptedContent);

            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $contentType = $fileInfo->buffer($content) ?: 'application/octet-stream';

            return $this->encryptionService->encryptResponse(
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
