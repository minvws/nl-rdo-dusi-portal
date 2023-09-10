<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;

readonly class ApplicationRetrievalService
{
    public function __construct(
        private EncryptionService $encryptionService,
        private ApplicationRepository $appRepo,
        private IdentityService $identityService,
        private ApplicationMapper $applicationMapper
    ) {
    }

    public function listApplications(ApplicationListParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if (empty($identity)) {
            // no identity found, so no applications (yet)
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::OK,
                new ApplicationList([]),
                $params->publicKey
            );
        }

        $apps = $this->appRepo->getMyApplications($identity);
        $list = $this->applicationMapper->mapApplicationArrayToApplicationListDTO($apps);

        return $this->encryptionService->encryptCodableResponse(
            EncryptedResponseStatus::OK,
            $list,
            $params->publicKey
        );
    }

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if ($identity === null) {
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('identity_not_found', 'Identity not registered yet.'),
                $params->publicKey
            );
        }

        $app = $this->appRepo->getMyApplication($identity, $params->reference);
        if ($app === null) {
            // application not found (for this identity)
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('application_not_found', 'Application not found.'),
                $params->publicKey
            );
        }

        // TODO: add data
        $data = $params->includeData ? (object)[] : null;
        $dto = $this->applicationMapper->mapApplicationToApplicationDTO($app, $data);

        return $this->encryptionService->encryptCodableResponse(
            EncryptedResponseStatus::OK,
            $dto,
            $params->publicKey
        );
    }
}
