<?php

/**
 * Application Mutation Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Application\Backend\Events\Logging\SaveApplicationEvent;
use MinVWS\DUSi\Application\Backend\Events\Logging\StartApplicationEvent;
use MinVWS\DUSi\Application\Backend\Events\Logging\SubmitApplicationEvent;
use MinVWS\DUSi\Application\Backend\Interfaces\FrontendDecryption;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FrontendDecryptionFailedException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Jobs\CheckSurePayJob;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationReferenceRepository;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFieldHookService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ValidationErrorException;
use MinVWS\DUSi\Shared\Application\Services\FormDecodingService;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationValidationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FieldValidationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ValidatedAndProcessedDataDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ValidationResultDTO;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\Logging\Laravel\LogService;
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
        private LoggerInterface $logger,
        private LogService $logService,
        private FormDecodingService $decodingService,
        private ApplicationFieldHookService $applicationFieldHookService,
        private ApplicationReferenceRepository $applicationReferenceRepository,
    ) {
    }

    public function createApplication(ApplicationCreateParams $params): EncryptedResponse
    {
        try {
            return DB::transaction(
                fn() => $this->doCreateApplication($params),
                self::CREATE_APPLICATION_ATTEMPTS
            );
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::CREATE_APPLICATION,
                $params->publicKey
            );
        }
    }

    /**
     * @param ApplicationCreateParams $params
     * @return EncryptedResponse
     * @throws EncryptedResponseException
     * @throws Exceptions\ApplicationReferenceException
     */
    private function doCreateApplication(ApplicationCreateParams $params): EncryptedResponse
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

        if (
            $this->applicationRepository->hasOpenOrApprovedApplicationsForSubsidy($identity, $subsidy) &&
            $subsidy->allow_multiple_applications === false
        ) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_does_not_allow_multiple_applications',
                logAsError: false
            );
        }


        if (
            !$subsidy->is_open_for_new_applications
        ) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_closed_for_new_applications',
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
        $this->applicationReferenceRepository->saveReference($application->reference);

        $this->logService->log((new StartApplicationEvent())
            ->withData([
                'reference' => $application->reference,
                'userId' => $identity->id,
            ]));

        [$encryptedKey] = $this->applicationEncryptionService->generateEncryptionKey();

        $appStage = $this->applicationRepository->makeApplicationStage($application, $subsidyStage);
        $appStage->encrypted_key = $encryptedKey;
        $appStage->sequence_number = 1;
        $appStage->is_current = true;
        $this->applicationRepository->saveApplicationStage($appStage);

        return $this->applicationResponse(
            EncryptedResponseStatus::CREATED,
            $application,
            null,
            $params->publicKey
        );
    }

    private function applicationResponse(
        EncryptedResponseStatus $status,
        Application $application,
        ?ValidationResultDTO $validationResult,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $stage = $this->applicationRepository->getCurrentApplicantApplicationStage($application, true);
        $data = $stage !== null ? $this->applicationDataService->getApplicationStageData($stage) : null;

        $dto = $this->applicationMapper->mapApplicationToApplicationDTO(
            $application,
            $data,
            $validationResult
        );

        return $this->responseEncryptionService->encryptCodable($status, $dto, $publicKey);
    }

    public function saveApplication(EncryptedApplicationSaveParams $params): EncryptedResponse
    {
        try {
            return DB::transaction(fn() => $this->doSaveApplication($params));
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

    /**
     * @throws Throwable
     * @throws FrontendDecryptionFailedException
     * @throws EncryptedResponseException
     */
    private function doSaveApplication(EncryptedApplicationSaveParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);
        $application = $this->loadApplication($identity, $params->applicationReference, lockForUpdate: true);
        $body = $this->frontendDecryptionService->decryptCodable($params->data, ApplicationSaveBody::class);

        if (
            $body->submit &&
            !$application->subsidyVersion->subsidy->is_open_for_new_applications &&
            !$application->status->isEditableForApplicantAfterClosure()
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
            $validationResult = $this->applicationDataService->saveApplicationStageData(
                $applicationStage,
                $body->data,
                $body->submit
            );

            $this->logService->log((new SaveApplicationEvent())
                ->withData([
                    'reference' => $application->reference,
                    'userId' => $identity->id,
                ]));
        } catch (ValidationErrorException $e) {
            $this->logger->debug('Validation returns errors', [
                'validationResult' => $e->getValidationResults(),
            ]);

            return $this->responseEncryptionService->encryptCodable(
                EncryptedResponseStatus::UNPROCESSABLE_ENTITY,
                new ValidationResultDTO($e->getValidationResults()),
                $params->publicKey
            );
        }

        if ($body->submit) {
            $this->applicationFlowService->submitApplicationStage($applicationStage);

            $this->logService->log((new SubmitApplicationEvent())
                ->withData([
                    'reference' => $application->reference,
                    'userId' => $identity->id,
                ]));

            // TODO: this should be generalized
            DB::afterCommit(fn() => CheckSurePayJob::dispatch($application->id));
        }

        return $this->applicationResponse(
            EncryptedResponseStatus::OK,
            $applicationStage->application,
            new ValidationResultDTO($validationResult),
            $params->publicKey
        );
    }

    public function validateApplication(EncryptedApplicationValidationParams $params): EncryptedResponse
    {
        try {
            $identity = $this->loadIdentity($params->identity);
            $application = $this->loadApplication($identity, $params->applicationReference);
            $applicationStage = $application->currentApplicationStage;
            if ($applicationStage === null) {
                throw new EncryptedResponseException(
                    EncryptedResponseStatus::FORBIDDEN,
                    'application_readonly'
                );
            }
            $body = $this->frontendDecryptionService->decryptCodable($params->data, FieldValidationParams::class);

            $fieldValues = $this->decodingService->decodeFormValues($applicationStage->subsidyStage, $body->data);
            $fieldValues = $this->applicationFieldHookService->findAndExecuteHooks($fieldValues, $applicationStage);

            $data = array_map(function (FieldValue $fieldValue) {
                return $fieldValue->value;
            }, $fieldValues);

            $validationResult = [];
            try {
                $validationResult = $this->applicationDataService->validateFieldValues(
                    $applicationStage,
                    $fieldValues,
                    false
                );
            } catch (ValidationErrorException $e) {
                return $this->responseEncryptionService->encryptCodable(
                    EncryptedResponseStatus::UNPROCESSABLE_ENTITY,
                    $this->createValidatedAndProcessedDataDTO($data, $e->getValidationResults()),
                    $params->publicKey
                );
            }

            return $this->responseEncryptionService->encryptCodable(
                EncryptedResponseStatus::OK,
                $this->createValidatedAndProcessedDataDTO($data, $validationResult),
                $params->publicKey
            );
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::VALIDATE_APPLICATION,
                $params->publicKey
            );
        }
    }

    public function createValidatedAndProcessedDataDTO(
        array $data,
        array $validationResults
    ): ValidatedAndProcessedDataDTO {
        return new ValidatedAndProcessedDataDTO(
            data: $data,
            validationResult: $validationResults,
        );
    }
}
