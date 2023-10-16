<?php

/**
 * Application Mutation Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Application\Backend\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Jobs\CheckSurePayJob;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFindOrCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
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

    private const CREATE_APPLICATION_ATTEMPTS = 3;

    public function __construct(
        private ApplicationDataService $applicationDataService,
        private ApplicationStageEncryptionService $applicationEncryptionService,
        private ApplicationFlowService $applicationFlowService,
        private ResponseEncryptionService $responseEncryptionService,
        private IdentityService $identityService,
        private ApplicationRepository $applicationRepository,
        private SubsidyRepository $subsidyRepository,
        private ApplicationMapper $applicationMapper,
        private ApplicationReferenceService $applicationReferenceService,
        private FrontendDecryption $frontendDecryptionService,
        private EncryptedResponseExceptionHelper $exceptionHelper,
        private LoggerInterface $logger
    ) {
    }

    private function applicationResponse(
        EncryptedResponseStatus $status,
        Application $application,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $stage = $this->applicationRepository->getApplicantApplicationStage($application, true);
        $data = $stage !== null ? $this->applicationDataService->getApplicationStageData($stage) : null;

        $dto = $this->applicationMapper->mapApplicationToApplicationDTO(
            $application,
            $data
        );

        return $this->responseEncryptionService->encryptCodable($status, $dto, $publicKey);
    }

    /**
     * @param ApplicationFindOrCreateParams $params
     * @return EncryptedResponse
     * @throws EncryptedResponseException
     * @throws Exceptions\ApplicationReferenceException
     */
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
                'subsidy_not_found'
            );
        }

        $identity = $this->identityService->findOrCreateIdentity($params->identity);

        $application = $this->applicationRepository->findMyApplicationForSubsidy($identity, $subsidy);

        if (
            !$subsidy->is_open_for_new_applications &&
            $application?->status !== ApplicationStatus::RequestForChanges
        ) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_closed_for_new_applications',
                logAsError: false
            );
        }

        if ($application?->is_editable_for_applicant) {
            return $this->applicationResponse(EncryptedResponseStatus::OK, $application, $params->publicKey);
        }

        if ($application?->status?->isNewApplicationAllowed()) {
            // ignore existing and create a new one
            $application = null;
        }

        if ($application !== null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_already_exists',
                logAsError: false
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
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::FIND_OR_CREATE_APPLICATION,
                $params->publicKey
            );
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

        if (
            $body->submit &&
            !$application->subsidyVersion->subsidy->is_open_for_new_applications &&
            $application->status !== ApplicationStatus::RequestForChanges
        ) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_closed_for_new_applications',
            );
        }

        if (!$application->is_editable_for_applicant) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly',
            );
        }

        $applicationStage = $application->currentApplicationStage;
        if ($applicationStage === null || $applicationStage->subsidyStage->stage !== 1) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'application_readonly'
            );
        }

        try {
            $this->applicationDataService->saveApplicationStageData($applicationStage, $body->data, $body->submit);
        } catch (ValidationException $e) {
            $this->logger->debug('Data validation failed', [
                'errors' => $e->errors(),
            ]);

            throw new EncryptedResponseException(
                EncryptedResponseStatus::BAD_REQUEST,
                'invalid_data',
                previous: $e
            );
        }

        if ($body->submit) {
            $this->applicationFlowService->submitApplicationStage($applicationStage);

            // TODO: this should be generalized
            DB::afterCommit(fn () => CheckSurePayJob::dispatch($application->id));
        }

        return $this->applicationResponse(
            EncryptedResponseStatus::OK,
            $applicationStage->application,
            $params->publicKey
        );
    }

    public function saveApplication(EncryptedApplicationSaveParams $params): EncryptedResponse
    {
        try {
            return DB::transaction(fn () => $this->doSaveApplication($params));
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::SAVE_APPLICATION,
                $params->publicKey
            );
        }
    }
}
