<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\HandleException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationRetrievalService
{
    use HandleException;
    use LoadIdentity;
    use LoadApplication;

    public function __construct(
        private ApplicationDataService $applicationDataService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository,
        private IdentityService $identityService,
        private ApplicationMapper $applicationMapper,
        private LoggerInterface $logger
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

        $apps = $this->applicationRepository->getMyApplications($identity);
        $list = $this->applicationMapper->mapApplicationArrayToApplicationListDTO($apps);

        return $this->encryptionService->encryptCodableResponse(
            EncryptedResponseStatus::OK,
            $list,
            $params->publicKey
        );
    }

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        try {
            $identity = $this->loadIdentity($params->identity);
            $app = $this->loadApplication($identity, $params->reference);

            $data = null;
            if ($params->includeData) {
                $data = $this->applicationDataService->getApplicationData($app);
            }

            $dto = $this->applicationMapper->mapApplicationToApplicationDTO($app, $data);

            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::OK,
                $dto,
                $params->publicKey
            );
        } catch (Throwable $e) {
            return $this->handleException(__METHOD__, $e, $params->publicKey);
        }
    }
}
