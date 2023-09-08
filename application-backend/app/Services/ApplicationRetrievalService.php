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
            return $this->encryptionService->encryptResponse(
                EncryptedResponseStatus::OK,
                new ApplicationList([]),
                $params->publicKey
            );
        }

        $apps = $this->appRepo->getMyApplications($identity);
        $list = $this->applicationMapper->mapApplicationArrayToApplicationListDTO($apps);

        return $this->encryptionService->encryptResponse(
            EncryptedResponseStatus::OK,
            $list,
            $params->publicKey
        );
    }

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);

        if (!empty($identity)) {
            $app = $this->appRepo->getMyApplication($identity, $params->reference);
        }

        if (empty($identity) || empty($app)) {
            // no identity found, so no application yet, unknown application, or application of other user
            return $this->encryptionService->encryptResponse(
                EncryptedResponseStatus::NOT_FOUND,
                null,
                $params->publicKey
            );
        }

        // TODO: add data
        $data = $params->includeData ? (object)[] : null;
        $dto = $this->applicationMapper->mapApplicationToApplicationDTO($app, $data);

        return $this->encryptionService->encryptResponse(
            EncryptedResponseStatus::OK,
            $dto,
            $params->publicKey
        );
    }
}
