<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Services\Traits\HandleException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use Psr\Log\LoggerInterface;
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
        private ApplicationRepository $applicationRepository,
        private IdentityService $identityService,
        private LoggerInterface $logger
    ) {
    }

    public function getApplicationFile(ApplicationFileParams $params): EncryptedResponse
    {
        try {
            $identity = $this->loadIdentity($params->identity);
            $app = $this->loadApplication($identity, $params->applicationReference);

            // TODO: retrieve file content
            $content = 'TODO ' . $app->reference;
            $contentType = 'text/plain';

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

    public function deleteApplicationFile(ApplicationFileParams $params): EncryptedResponse
    {
        try {
            $identity = $this->loadIdentity($params->identity);
            $app = $this->loadApplication($identity, $params->applicationReference);

            if (!$app->status->isEditableForApplicant()) {
                throw new EncryptedResponseException(
                    EncryptedResponseStatus::FORBIDDEN,
                    'application_readonly',
                    'Application is read-only.'
                );
            }

            // TODO: delete file

            return $this->encryptionService->encryptResponse(
                EncryptedResponseStatus::NO_CONTENT,
                '',
                '',
                $params->publicKey
            );
        } catch (Throwable $e) {
            return $this->handleException(__METHOD__, $e, $params->publicKey);
        }
    }
}
