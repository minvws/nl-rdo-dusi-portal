<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Console\Commands;

use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\SurePayService;
use Ramsey\Uuid\Uuid;

class CheckSurePay extends Command
{
    protected $signature = 'sure-pay:check {applicationId}';
    protected $description = 'Run SurePay check for the given application (only works for PZCM applications!)';

    /**
     * Execute the console command.
     */
    public function handle(SurePayService $surePayService, ApplicationRepository $applicationRepository): int
    {
        $applicationId = $this->argument('applicationId');

        if (!is_string($applicationId) || !Uuid::isValid($applicationId)) {
            $this->output->error('Please provide a valid application identifier!');
            return self::INVALID;
        }

        $application = $applicationRepository->getApplication($applicationId);
        if ($application === null) {
            $this->output->error('Application not found!');
            return self::FAILURE;
        }


        if (!$surePayService->shouldCheckSurePayForApplication($application)) {
            $this->output->error('SurePay disabled or not supported for the subsidy of the provided application!');
            return self::INVALID;
        }

        $result = $surePayService->checkSurePayForApplication($application);
        if ($result === null) {
            $this->output->error('SurePay check failed, check logs!');
            return self::FAILURE;
        }

        $headers = ['Field', 'Value'];
        $rows = [];
        foreach ($result->toArray() as $key => $value) {
            $rows[] = [$key, $value];
        }
        $this->output->table($headers, $rows);

        return self::SUCCESS;
    }
}
