<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Mappers\SubsidyMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyOverview;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyOverviewParams;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SubsidyService
{
    use LoadIdentity;

    public function __construct(
        private ApplicationRepository $applicationRepository,
        private ResponseEncryptionService $responseEncryptionService,
        private IdentityService $identityService,
        private SubsidyRepository $subsidyRepository,
        private SubsidyMapper $subsidyMapper,
        private ApplicationMapper $applicationMapper,
        private EncryptedResponseExceptionHelper $exceptionHelper,
    ) {
    }

    public function getSubsidyOverview(SubsidyOverviewParams $params): EncryptedResponse
    {
        try {
            return $this->doGetSubsidyOverview($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_SUBSIDY_OVERVIEW,
                $params->publicKey
            );
        }
    }

    private function doGetSubsidyOverview(SubsidyOverviewParams $params): EncryptedResponse
    {
        $subsidy = $this->subsidyRepository->findSubsidyByCode($params->subsidyCode);

        if ($subsidy === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'subsidy_not_found'
            );
        }

        $identity = $this->identityService->findIdentity($params->identity);

        $subsidyOverview = $this->getSubsidyOverviewResult($identity, $subsidy);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $subsidyOverview,
            $params->publicKey
        );
    }

    private function getNewConceptAllowed(Identity $identity, Subsidy $subsidy): bool
    {
        if (!$subsidy->is_open_for_new_applications) {
            return false;
        }

        if (!$this->applicationRepository->hasOpenOrApprovedApplicationsForSubsidy($identity, $subsidy)) {
            return true;
        }

        return $subsidy->allow_multiple_applications;
    }

    private function getApplications(Identity $identity, Subsidy $subsidy): ApplicationList
    {
        $applications = $this->applicationRepository->getMyApplications($identity, $subsidy);
        return $this->applicationMapper->mapApplicationsToApplicationListDTO($applications);
    }

    private function getSubsidyOverviewResult(?Identity $identity, Subsidy $subsidy): SubsidyOverview
    {
        $subsidyDto = $this->subsidyMapper->mapSubsidyVersionToSubsidyDTO($subsidy->publishedVersion, $subsidy);

        if (!$identity) {
            return new SubsidyOverview(
                subsidy: $subsidyDto,
                newConceptAllowed: true,
                hasApprovedApplication: false,
                hasRejectedApplication: false,
                applications: [],
            );
        }

        $newConceptAllowed = $this->getNewConceptAllowed($identity, $subsidy);
        $hasApprovedApplications = $this->applicationRepository->hasApprovedApplicationForSubsidy($identity, $subsidy);
        $hasRejectedApplications = $this->applicationRepository->hasRejectedApplicationForSubsidy($identity, $subsidy);
        $applicationList = $this->getApplications($identity, $subsidy);

        return new SubsidyOverview(
            subsidy: $subsidyDto,
            newConceptAllowed: $newConceptAllowed,
            hasApprovedApplication: $hasApprovedApplications,
            hasRejectedApplication: $hasRejectedApplications,
            applications: $applicationList->items,
        );
    }
}
