<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Carbon\Carbon;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFindOrCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
readonly class ApplicationMutationService
{
    use LoadIdentity;
    use LoadApplication;

    private const CREATE_APPLICATION_ATTEMPTS = 3;

    public function __construct(
        private ApplicationService $applicationService,
        private ApplicationDataService $applicationDataService,
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private IdentityService $identityService,
        private ApplicationRepository $applicationRepository,
        private SubsidyRepository $subsidyRepository,
        private ApplicationMapper $applicationMapper,
        private ApplicationReferenceService $applicationReferenceService,
        private FrontendDecryption $frontendDecryptionService,
    ) {
    }

    private function applicationResponse(
        EncryptedResponseStatus $status,
        Application $application,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $dto = $this->applicationMapper->mapApplicationToApplicationDTO(
            $application,
            $this->applicationDataService->getApplicationData($application),
            $this->applicationDataService->getApplicationFiles($application)
        );

        return $this->encryptionService->encryptCodableResponse($status, $dto, $publicKey);
    }

    private function doFindOrCreateApplication(ApplicationFindOrCreateParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findOrCreateIdentity($params->identity);
        $subsidy = $this->subsidyRepository->findSubsidyByCode($params->subsidyCode);
        if ($subsidy === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_not_found',
                'Subsidy with the given code does not exist.'
            );
        }

        $application = $this->applicationRepository->findMyApplicationForSubsidy($identity, $subsidy);
        if ($application !== null && $application->status->isEditableForApplicant()) {
            return $this->applicationResponse(EncryptedResponseStatus::OK, $application, $params->publicKey);
        }

        if ($application !== null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_already_exists',
                'There is already a submitted application for this identity.'
            );
        }

        $subsidyVersion = $subsidy->publishedVersion;
        $subsidyStage = $this->subsidyRepository->getFirstStageForSubsidyVersion($subsidyVersion);

        $application = $this->applicationRepository->makeApplicationForIdentityAndSubsidyVersion(
            $identity,
            $subsidyVersion
        );
        $application->application_title = $subsidyVersion->subsidy->title;
        $application->status = ApplicationStatus::Draft;

        $application->reference = $this->applicationReferenceService->generateUniqueReferenceByElevenRule(
            $application->subsidyVersion->subsidy
        );
        $this->applicationRepository->saveApplication($application);

        $appStage = $this->applicationRepository->makeApplicationStage($application, $subsidyStage);
        $appStage->sequence_number = 1;
        $appStage->is_current = true;
        $this->applicationRepository->saveApplicationStage($appStage);

        return $this->applicationResponse(EncryptedResponseStatus::CREATED, $application, $params->publicKey);
    }

    public function findOrCreateApplication(ApplicationFindOrCreateParams $params): EncryptedResponse
    {
        return DB::transaction(
            function () use ($params) {
                try {
                    return $this->doFindOrCreateApplication($params);
                } catch (EncryptedResponseException $e) {
                    return $this->encryptionService->encryptCodableResponse(
                        $e->getStatus(),
                        $e->getError(),
                        $params->publicKey
                    );
                }
            },
            self::CREATE_APPLICATION_ATTEMPTS
        );
    }

    private function doSaveApplication(EncryptedApplicationSaveParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);
        $application = $this->loadApplication($identity, $params->applicationReference);
        $body = $this->frontendDecryptionService->decryptCodable($params->data, ApplicationSaveBody::class);

        if (!$application->status->isEditableForApplicant()) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly',
                'Application is read-only'
            );
        }

        $applicationStage = $application->currentApplicationStage;
        if ($applicationStage->subsidyStage->stage !== 1) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly',
                'Application is read-only'
            );
        }

        $values = $this->decodingService->decodeFormValues($applicationStage->subsidyStage, $body->data);

        // TODO: Validation will be in other PR
        $this->applicationService->processFieldValues($applicationStage, $values);

        if ($body->status === ApplicationStatus::Submitted) {
            $application->status = ApplicationStatus::Submitted;
            $application->final_review_deadline =
                Carbon::now()->addDays($applicationStage->application->subsidyVersion->review_period);
            $this->applicationRepository->saveApplication($application);

            $applicationStage->is_current = false;

            // TODO: insert next application stage
            // $this->appRepo->makeApplicationStage($applicationStage->application, $nextSubsidyStage);
        }

        $this->applicationRepository->saveApplicationStage($applicationStage);
        return $this->applicationResponse(EncryptedResponseStatus::OK, $application, $params->publicKey);
    }

    public function saveApplication(EncryptedApplicationSaveParams $params): EncryptedResponse
    {
        return DB::transaction(function () use ($params) {
            try {
                return $this->doSaveApplication($params);
            } catch (EncryptedResponseException $e) {
                return $this->encryptionService->encryptCodableResponse(
                    $e->getStatus(),
                    $e->getError(),
                    $params->publicKey
                );
            }
        });
    }
}
