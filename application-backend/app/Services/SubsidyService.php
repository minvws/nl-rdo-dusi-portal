<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Application\Backend\Mappers\SubsidyMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationConcept;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConcepts;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConceptsParams;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

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
    ) {
    }

    public function getSubsidyAndConcepts(SubsidyConceptsParams $params): EncryptedResponse
    {
        $subsidy = $this->subsidyRepository->findSubsidyByCode($params->subsidyCode);

        if ($subsidy === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_not_found'
            );
        }

        $identity = $this->identityService->findOrCreateIdentity($params->identity, lockForUpdate: true);

        $applications = $this->applicationRepository->getMyConceptApplications($identity, $subsidy);

        $subsidyDto = $this->subsidyMapper->mapSubsidyToSubsidyDTO($subsidy);
        $concepts = $this->mapConceptApplicationsToApplicationConcepts($applications);

        $subsidyConcepts = new SubsidyConcepts($subsidyDto, $concepts);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $subsidyConcepts,
            $params->publicKey
        );
    }

    /**
     * @psalm-suppress InvalidTemplateParam
     *
     * @param Collection $applications<array-key, mixed>
     */
    private function mapConceptApplicationsToApplicationConcepts(Collection $applications): array
    {
        if ($applications->isEmpty()) {
            return [];
        }

        return $applications->map(function (Application $application) {
            return $application->applicationStages->map(
                function (ApplicationStage $applicationStage) use ($application) {
                    return new ApplicationConcept(
                        $application->reference,
                        $application->subsidyVersion->subsidy->code,
                        $applicationStage->subsidy_stage_id,
                        $applicationStage->created_at,
                        $applicationStage->updated_at,
                        $applicationStage->expires_at,
                        $applicationStage->submitted_at,
                        $application->final_review_deadline,
                        $application->status,
                        $application->status->isEditableForApplicant()
                    );
                }
            );
        })->toArray();
    }
}
