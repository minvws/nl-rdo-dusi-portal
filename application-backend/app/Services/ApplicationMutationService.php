<?php

/**
 * Application Mutation Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;
use MinVWS\DUSi\Application\Backend\Services\Traits\HandleException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
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
    use HandleException;

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

        try {
            $this->applicationDataService->saveApplicationStageData($applicationStage, $body->data, $body->submit);
        } catch (ValidationException $e) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::BAD_REQUEST,
                'invalid_data',
                'Data contains invalid values',
                previous: $e
            );
        }

        if ($body->submit) {
            $this->applicationFlowService->submitApplicationStage($applicationStage);
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
            return $this->handleException(__METHOD__, $e, $params->publicKey);
        }
    }
}
