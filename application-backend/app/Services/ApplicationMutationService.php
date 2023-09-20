<?php

/**
 * Application Mutation Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Carbon\Carbon;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;
use MinVWS\DUSi\Application\Backend\Services\Traits\HandleException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationEncryptionService;
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
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
readonly class ApplicationMutationService
{
    use LoadIdentity;
    use LoadApplication;
    use HandleException;

    private const CREATE_APPLICATION_ATTEMPTS = 3;

    public function __construct(
        private ApplicationDataService $applicationDataService,
        private ApplicationEncryptionService $applicationEncryptionService,
        private ResponseEncryptionService $responseEncryptionService,
        private IdentityService $identityService,
        private ApplicationRepository $applicationRepository,
        private SubsidyRepository $subsidyRepository,
        private ApplicationMapper $applicationMapper,
        private ApplicationReferenceService $applicationReferenceService,
        private FrontendDecryption $frontendDecryptionService,
        private LoggerInterface $logger
    ) {
    }

    private function applicationResponse(
        EncryptedResponseStatus $status,
        Application $application,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $dto = $this->applicationMapper->mapApplicationToApplicationDTO(
            $application,
            $this->applicationDataService->getApplicationData($application)
        );

        return $this->responseEncryptionService->encryptCodable($status, $dto, $publicKey);
    }

    private function doFindOrCreateApplication(ApplicationFindOrCreateParams $params): EncryptedResponse
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
                'subsidy_not_found',
                'Subsidy with the given code does not exist.'
            );
        }

        $identity = $this->identityService->findOrCreateIdentity($params->identity);

        $application = $this->applicationRepository->findMyApplicationForSubsidy($identity, $subsidy);
        if ($application !== null && $application->status->isEditableForApplicant()) {
            return $this->applicationResponse(EncryptedResponseStatus::OK, $application, $params->publicKey);
        }

        if ($application !== null && $application->status->isNewApplicationAllowed()) {
            // ignore existing and create a new one
            $application = null;
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

        [$encryptedKey] = $this->applicationEncryptionService->generateEncryptionKey();

        $appStage = $this->applicationRepository->makeApplicationStage($application, $subsidyStage);
        $appStage->encrypted_key = $encryptedKey;
        $appStage->sequence_number = 1;
        $appStage->is_current = true;
        $this->applicationRepository->saveApplicationStage($appStage);

        return $this->applicationResponse(EncryptedResponseStatus::CREATED, $application, $params->publicKey);
    }

    public function findOrCreateApplication(ApplicationFindOrCreateParams $params): EncryptedResponse
    {
        try {
            return DB::transaction(
                fn () => $this->doFindOrCreateApplication($params),
                self::CREATE_APPLICATION_ATTEMPTS
            );
        } catch (Throwable $e) {
            return $this->handleException(__METHOD__, $e, $params->publicKey);
        }
    }

    /**
     * @throws Throwable
     * @throws FrontendDecryptionFailedException
     * @throws EncryptedResponseException
     */
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

        $this->applicationDataService->saveApplicationData($applicationStage, $body->data);

        if ($body->submit) {
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
        try {
            return DB::transaction(fn () => $this->doSaveApplication($params));
        } catch (Throwable $e) {
            return $this->handleException(__METHOD__, $e, $params->publicKey);
        }
    }
}
