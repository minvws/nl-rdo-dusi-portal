<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;

/**
 * This will process all the expired application stages that have a transition with the expiration trigger.
 *
 * An application has an expires_at, this is the date that the application stage expires. The user can still
 * submit an application on this date, the next day, the application stage is expire.
 * This command will process all the expired applications.
 */
class ProcessExpiredApplicationStagesCommand extends Command
{
    private const NAME = 'app:process-expired-application-stages';
    private const ARGS = '{--dry-run}';

    protected $signature = self::NAME . ' ' . self::ARGS;
    protected $description = 'Tries to transition expired application stages to the next stage';

    public function __construct(
        private readonly ApplicationFlowService $applicationFlowService,
        private readonly ApplicationRepository $applicationRepository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run') === true;

        $applicationStages = $this->applicationRepository->getExpiredApplicationStages();

        $total = $applicationStages->count();
        $this->info(sprintf('Found %d applications with expired stages', $total));

        foreach ($applicationStages as $applicationStage) {
            $this->info(sprintf('Processing application %s...', $applicationStage->application->reference));
            if ($dryRun) {
                $this->info('Skip (dry-run)');
                continue;
            }

            try {
                $newStage = $this->applicationFlowService->evaluateApplicationStage(
                    $applicationStage,
                    EvaluationTrigger::Expiration
                );

                $this->info(
                    sprintf(
                        'Transitioned application %s to %s stage',
                        $applicationStage->application->reference,
                        $newStage?->subsidyStage?->title ?? 'no'
                    )
                );
            } catch (ApplicationFlowException $e) {
                $this->error(
                    sprintf(
                        'Error processing application %s: %s',
                        $applicationStage->application->reference,
                        $e->getMessage()
                    )
                );
            }
        }

        $this->info('Operation completed!');

        return self::SUCCESS;
    }
}
