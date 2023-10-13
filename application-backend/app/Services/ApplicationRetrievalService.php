<?php

/**
 * Application Retrieval Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationRetrievalService
{
    use LoadIdentity;
    use LoadApplication;

    public function __construct(
        private ApplicationDataService $applicationDataService,
        private ResponseEncryptionService $responseEncryptionService,
        private ApplicationRepository $applicationRepository,
        private IdentityService $identityService,
        private ApplicationMapper $applicationMapper,
        private EncryptedResponseExceptionHelper $exceptionHelper
    ) {
    }

    public function listApplications(ApplicationListParams $params): EncryptedResponse
    {
        try {
            return $this->doListApplications($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::LIST_APPLICATIONS,
                $params->publicKey
            );
        }
    }

    public function doListApplications(ApplicationListParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if ($identity === null) {
            // no identity found, so no applications (yet)
            return $this->responseEncryptionService->encryptCodable(
                EncryptedResponseStatus::OK,
                new ApplicationList([]),
                $params->publicKey
            );
        }

        $apps = $this->applicationRepository->getMyApplications($identity);
        $list = $this->applicationMapper->mapApplicationArrayToApplicationListDTO($apps);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $list,
            $params->publicKey
        );
    }

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        try {
            return $this->doGetApplication($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_APPLICATION,
                $params->publicKey
            );
        }
    }

    private function doGetApplication(ApplicationParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);
        $app = $this->loadApplication($identity, $params->reference);

        $data = null;
        if ($params->includeData) {
            $appStage = $this->applicationRepository->getApplicantApplicationStage($app, true);
            $data = $appStage !== null ? $this->applicationDataService->getApplicationStageData($appStage) : null;
        }

        $dto = $this->applicationMapper->mapApplicationToApplicationDTO($app, $data);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $dto,
            $params->publicKey
        );
    }
}
