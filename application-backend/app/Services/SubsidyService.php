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

        $identity = $this->identityService->findOrCreateIdentity($params->identity, lockForUpdate: true);

        $applications = $this->applicationRepository->getMyConceptApplications($identity, $subsidy);

        $subsidyDto = $this->subsidyMapper->mapSubsidyToSubsidyDTO($subsidy);
        $concepts = $this->mapConceptApplicationsToApplicationConcepts($applications);

        $newConceptsAllowed = $this->getNewConceptsAllowed($subsidy, $identity);

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

        /**
         * The Application model here is extended with the following properties:
         * - subsidy_stage_id
         * - created_at
         * - updated_at
         * - expires_at
         * This is done in the ApplicationRepository::getMyConceptApplications function.
         */
        return $applications->map(function (Application $application) {
            return new ApplicationConcept(
                $application->reference,
                $application->subsidyVersion->subsidy->code,
                $application->subsidy_stage_id, // @phpstan-ignore-line
                CarbonImmutable::parse($application->created_at),
                CarbonImmutable::parse($application->updated_at),
                // @phpstan-ignore-next-line
                $application->expires_at ? CarbonImmutable::parse($application->expires_at) : null,
                $application->status,
            );
        })->toArray();
    }

    private function getNewConceptsAllowed(Subsidy $subsidy, Identity $identity): bool
    {
        if (!$subsidy->is_open_for_new_applications) {
            return false;
        }

        if (!$this->applicationRepository->hasOpenOrApprovedApplicationsForSubsidy($identity, $subsidy)) {
            return true;
        }

        return $subsidy->allow_multiple_applications;
    }
}
