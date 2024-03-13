<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Support\Facades\Log;
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
use Ramsey\Uuid\Uuid;

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

        $applications = $this->applicationRepository->getMyConceptApplications($identity, $subsidy);

        $subsidyDto = $this->subsidyMapper->mapSubsidyToSubsidyDTO($subsidy);
        $concepts = $this->mapConceptApplicationsToApplicationConcepts($applications);

        $dto = new SubsidyConcepts($subsidyDto, $concepts);

        Log::debug(json_encode($dto));

        return $this->responseEncryptionService->encryptCodable(EncryptedResponseStatus::OK, $dto, $params->publicKey);
    }

    public function mapConceptApplicationsToApplicationConcepts(array $applications): array
    {
        return array_map(function (Application $application) {
            return $application->applicationStages->map(
                function (ApplicationStage $applicationStage) use ($application) {
                    return new ApplicationConcept(
                        $application->reference,
                        $application->subsidyVersion->subsidy->code,
                        $applicationStage->subsidy_stage_id,
                        $applicationStage->created_at,
                        $applicationStage->updated_at,
                        $applicationStage->expires_at,
                        $application->status,
                        $application->status->isEditableForApplicant()
                    );
                }
            );
        }, $applications);
    }
}
