<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConcepts;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConceptsParams;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
//use MinVWS\Logging\Laravel\Loggers\LoggerInterface;
use Ramsey\Uuid\Uuid;

class SubsidyService
{
    use LoadIdentity;

    public function __construct(
        private ApplicationRepository $applicationRepository,
        private ApplicationMapper $applicationMapper,
        private ApplicationStageEncryptionService $applicationEncryptionService,
        private ResponseEncryptionService $responseEncryptionService,
        private IdentityService $identityService,
        private SubsidyRepository $subsidyRepository,
        private EncryptedResponseExceptionHelper $exceptionHelper,
        //        private LoggerInterface $logger,
    ) {
    }

    public function getSubsidyAndConcepts(SubsidyConceptsParams $params): EncryptedResponse
    {
        if (Uuid::isValid($params->subsidyCode)) {
            // TODO: once frontend uses subsidy code, we can remove this code
            $subsidy = $this->subsidyRepository->getSubsidyStage($params->subsidyCode)?->subsidyVersion?->subsidy;
        } else {
            $subsidy = $this->subsidyRepository->findSubsidyByCode($params->subsidyCode);
        }

        if ($subsidy === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_not_found'
            );
        }

        $identity = $this->identityService->findOrCreateIdentity($params->identity, lockForUpdate: true);

        $apps = $this->applicationRepository->getMyApplications($identity);
        $concepts = $this->applicationMapper->mapApplicationArrayToApplicationListDTO($apps);

        $dto = new SubsidyConcepts($subsidy, $concepts);

        Log::debug(json_encode($dto));

        return $this->responseEncryptionService->encryptCodable(EncryptedResponseStatus::OK, $dto, $params->publicKey);
    }
}
