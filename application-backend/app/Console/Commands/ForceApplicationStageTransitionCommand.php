<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

class ForceApplicationStageTransitionCommand extends Command
{
    private const NAME = 'app:force-application-transition';
    private const ARGS = '{subsidyStageId} {subsidyStageTransitionId} {--resetClonedDataOfCurrentStage}';

    protected $signature = self::NAME . ' ' . self::ARGS;
    protected $description = 'Force transition of applications with the given current subsidy stage';

    public function __construct(private readonly ApplicationFlowService $applicationFlowService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $subsidyStageId = $this->argument('subsidyStageId');
        $subsidyStage = SubsidyStage::query()->find($subsidyStageId);
        if (! $subsidyStage instanceof SubsidyStage) {
            $this->error('Invalid subsidy stage identifier');
            return self::INVALID;
        }

        $subsidyStageTransitionId = $this->argument('subsidyStageTransitionId');
        $subsidyStageTransition = SubsidyStageTransition::query()->find($subsidyStageTransitionId);
        if (! $subsidyStageTransition instanceof SubsidyStageTransition) {
            $this->error('Invalid subsidy stage transition identifier');
            return self::INVALID;
        }

        $resetClonedDataOfCurrentStage = $this->option('resetClonedDataOfCurrentStage');
        if (!is_bool($resetClonedDataOfCurrentStage)) {
            $this->error('Invalid value for reset cloned data of current stage');
            return self::INVALID;
        }

        $applicationStages =
            ApplicationStage::query()
                ->where('subsidy_stage_id', '=', $subsidyStage->id)
                ->where('is_current', '=', true)
                ->orderBy('id');

        $total = $applicationStages->count();
        $this->info(sprintf('Found %d applications', $total));

        if ($total > 0) {
            $bar = $this->output->createProgressBar($total);
            $bar->setFormat('verbose');

            $applicationStages->chunk(
                100,
                function ($applicationStages) use ($subsidyStageTransition, $resetClonedDataOfCurrentStage, $bar) {
                    foreach ($applicationStages as $applicationStage) {
                        $this->applicationFlowService->forceTransitionForApplicationStage(
                            $applicationStage,
                            $subsidyStageTransition,
                            $resetClonedDataOfCurrentStage
                        );
                        $bar->advance();
                    }
                }
            );

            $bar->finish();
            $this->info('');
        }

        $this->info('Operation completed!');

        return self::SUCCESS;
    }
}
