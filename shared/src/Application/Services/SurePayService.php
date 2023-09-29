<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;

class SurePayService
{
    private const SUBSIDY_PZCM_ID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

    public function __construct(
        private readonly ?SurePayClient $surePayClient,
        private readonly ApplicationDataService $applicationDataService,
        private readonly ApplicationRepository $applicationRepository
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
        $model->account_number_validation = $result->accountNumberValidation;
        $model->payment_pre_validation = $result->paymentPreValidation;
        $model->status = $result->status;
        $model->account_type = $result->accountType;
        $model->joint_account = $result->jointAccount;
        $model->number_of_account_holders = $result->numberOfAccountHolders;
        $model->country_code = $result->countryCode;
        $model->save();

        return $model;
    }
}
