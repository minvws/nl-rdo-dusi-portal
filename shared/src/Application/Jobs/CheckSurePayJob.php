<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountNumberValidation;
use MinVWS\DUSi\Shared\Application\Services\SurePayService;
use Throwable;

class CheckSurePayJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        public readonly string $applicationId
    ) {
    }

    public function handle(ApplicationRepository $applicationRepository, SurePayService $surePayService): void
    {
        $application = $applicationRepository->getApplication($this->applicationId);
        if ($application === null) {
            Log::error(
                sprintf(
                    "Error checking SurePay for application %s, application not found!",
                    $this->applicationId
                )
            );

            return;
        }

        try {
            Log::info('Performing SurePay check for application ' . $application->id);
            $result = $surePayService->checkSurePayForApplication($application);
            Log::info(
                sprintf(
                    'SurePay check for application %s finished, with result %s',
                    $application->id,
                    $result?->account_number_validation?->value ?? '-'
                )
            );
        } catch (Throwable $e) {
            Log::error('SurePay check failed with error: ' . $e->getMessage());
            Log::error("Stacktrace:\n" . $e->getTraceAsString());
        }
    }
}
