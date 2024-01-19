<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\BankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SurePayService
{
    private const SUBSIDY_PZCM_ID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';
    private const SUBSIDY_BTV_UUID = '00f26400-7232-475f-922c-6b569b7e421a';

    public function __construct(
        private readonly BankAccountRepository $bankAccountRepository,
        private readonly ApplicationDataService $applicationDataService,
        private readonly ApplicationRepository $applicationRepository,
        private readonly ApplicationStageEncryptionService $encryptionService,
    ) {
    }

    public function shouldCheckSurePayForApplication(Application $application): bool
    {
        // temporary until we have generalized this
        return $application->subsidyVersion->subsidy_id === self::SUBSIDY_PZCM_ID ||
            $application->subsidyVersion->subsidy_id === self::SUBSIDY_BTV_UUID;
    }

    /**
     * @throws ValidationException
     */
    public function checkSurePayForApplication(Application $application): ?ApplicationSurePayResult
    {
        if (!$this->shouldCheckSurePayForApplication($application)) {
            return null;
        }

        $stage = $this->applicationRepository->getCurrentApplicantApplicationStage($application, true);
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

        $result = $this->bankAccountRepository->checkOrganisationsAccount(
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

        if (!empty($result->nameSuggestion)) {
            $encrypter = $this->encryptionService->getEncrypter($stage);
            $model->encrypted_name_suggestion = $encrypter->encrypt($result->nameSuggestion);
        }
        $model->save();

        return $model;
    }

    /**
     * @throws ValidationException
     */
    public function checkOrganisationsAccount(
        string $accountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsAccountResponse {

        return $this->bankAccountRepository->checkOrganisationsAccount($accountOwner, $accountNumber, $accountType);
    }
}
