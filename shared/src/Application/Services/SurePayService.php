<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SurePayAccountCheckParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SurePayAccountCheckResult;
use RuntimeException;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SurePayService
{
    private const SUBSIDY_PZCM_ID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

    public function __construct(
        private readonly ?SurePayClient $surePayClient,
        private readonly ApplicationDataService $applicationDataService,
        private readonly ApplicationRepository $applicationRepository,
        private readonly EncryptedResponseExceptionHelper $exceptionHelper,
        private readonly ResponseEncryptionService $responseEncryptionService,
    ) {
    }

    public function shouldCheckSurePayForApplication(Application $application): bool
    {
        // temporary until we have generalized this
        return $this->surePayClient !== null && $application->subsidyVersion->subsidy_id === self::SUBSIDY_PZCM_ID;
    }

    public function checkSurePayForApplication(Application $application): ?ApplicationSurePayResult
    {
        if (!$this->shouldCheckSurePayForApplication($application) || $this->surePayClient === null) {
            return null;
        }

        $stage = $this->applicationRepository->getApplicantApplicationStage($application, true);
        if ($stage === null) {
            Log::error('SurePay check not possible, no applicant stage for application ' . $application->id);
            return null;
        }

        $data = $this->applicationDataService->getApplicationStageData($stage);
        if (
            !isset($data->bankAccountHolder) || !isset($data->bankAccountNumber) ||
            !is_string($data->bankAccountHolder) || !is_string($data->bankAccountNumber)
        ) {
            Log::error(
                'SurePay check not possible, applicant stage data invalid for application ' . $application->id
            );
            return null;
        }

        $result = $this->surePayClient->checkOrganisationsAccount(
            $data->bankAccountHolder,
            $data->bankAccountNumber
        );

        $model = ApplicationSurePayResult::firstOrNew(['application_id' => $application->id]);
        $model->name_match_result = $result->nameMatchResult;
        $model->account_number_validation = $result->account->accountNumberValidation;
        $model->payment_pre_validation = $result->account->paymentPreValidation;
        $model->status = $result->account->status;
        $model->account_type = $result->account->accountType;
        $model->joint_account = $result->account->jointAccount;
        $model->number_of_account_holders = $result->account->numberOfAccountHolders;
        $model->country_code = $result->account->countryCode;
        $model->save();

        return $model;
    }


    public function accountCheck(SurePayAccountCheckParams $params): EncryptedResponse
    {
        try {
            return $this->doAccountCheck($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::SUREPAY_ACCOUNT_CHECK,
                $params->publicKey
            );
        }
    }

    /**
     * @param SurePayAccountCheckParams $params
     * @return EncryptedResponse
     * @throws ValidationException
     */
    private function doAccountCheck(SurePayAccountCheckParams $params): EncryptedResponse
    {
        if ($this->surePayClient === null) {
            throw new RuntimeException('surePayClient is not set');
        }

        $checkOrganisationsAccountResponse = $this->surePayClient->checkOrganisationsAccount(
            $params->bankAccountHolder,
            $params->bankAccountNumber
        );

        $surePayAccountCheckResult = new SurePayAccountCheckResult(
            $checkOrganisationsAccountResponse->nameMatchResult,
            $checkOrganisationsAccountResponse->nameSuggestion
        );

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $surePayAccountCheckResult,
            $params->publicKey
        );
    }
}
