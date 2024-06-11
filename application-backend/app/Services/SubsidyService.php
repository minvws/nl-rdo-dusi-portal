<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Application\Backend\Mappers\SubsidyMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationConcept;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConcepts;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConceptsParams;
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
        private EncryptedResponseExceptionHelper $exceptionHelper,
    ) {
    }


    public function getSubsidyAndConcepts(SubsidyConceptsParams $params): EncryptedResponse
    {
        try {
            return $this->doGetSubsidyAndConcepts($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_SUBSIDY_CONCEPTS,
                $params->publicKey
            );
        }
    }

    private function doGetSubsidyAndConcepts(SubsidyConceptsParams $params): EncryptedResponse
    {
        $subsidy = $this->subsidyRepository->findSubsidyByCode($params->subsidyCode);

        if ($subsidy === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'subsidy_not_found'
            );
        }

        $identity = $this->identityService->findIdentity($params->identity);

        $subsidyDto = $this->subsidyMapper->mapSubsidyToSubsidyDTO($subsidy);
        $newConceptsAllowed = $this->getNewConceptsAllowed($identity, $subsidy);
        $concepts = $this->getConceptApplications($identity, $subsidy);

        $subsidyConcepts = new SubsidyConcepts(
            subsidy: $subsidyDto,
            newConceptAllowed: $newConceptsAllowed,
            concepts: $concepts,
        );

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $subsidyConcepts,
            $params->publicKey
        );
    }

    /**
     * @psalm-suppress InvalidTemplateParam
     *
     * @param Collection<array-key, Application> $applications
     * @return ApplicationConcept[]
     */
    private function mapConceptApplicationsToApplicationConcepts(Collection $applications): array
    {
        if ($applications->isEmpty()) {
            return [];
        }

        return $applications->map(function (Application $application) {
            return new ApplicationConcept(
                $application->reference,
                $application->subsidyVersion->subsidy->code,
                CarbonImmutable::parse($application->created_at),
                CarbonImmutable::parse($application->updated_at),
                $application->lastApplicationStage->expires_at
                    ? CarbonImmutable::parse($application->lastApplicationStage->expires_at)
                    : null,
                $application->status,
            );
        })->toArray();
    }

    private function getNewConceptsAllowed(?Identity $identity, Subsidy $subsidy): bool
    {
        if (!$subsidy->is_open_for_new_applications) {
            return false;
        }

        if (!$identity) {
            return true;
        }

        if (!$this->applicationRepository->hasOpenOrApprovedApplicationsForSubsidy($identity, $subsidy)) {
            return true;
        }

        return $subsidy->allow_multiple_applications;
    }

    /**
     * @return ApplicationConcept[]
     */
    private function getConceptApplications(?Identity $identity, Subsidy $subsidy): array
    {
        if (!$identity) {
            return [];
        }


        $applications = $this->applicationRepository->getMyConceptApplications($identity, $subsidy);
        return $this->mapConceptApplicationsToApplicationConcepts($applications);
    }
}
