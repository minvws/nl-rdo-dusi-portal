<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Events\ApplicationStageDecidedEvent;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStageDecision;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use RuntimeException;

class GeneratePdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-pdf {--applicationStageId=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temporary command to trigger letter generation';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $force = $this->option('force');
        if (!$force && !app()->environment('local')) {
            throw new RuntimeException('This command can only be use locally');
        }

        if ($this->option('applicationStageId')) {
            $applicationStages = [ApplicationStage::findOrFail($this->option('applicationStageId'))];
        } else {
            $applicationStages = $this->getApplicationStages();
        }

        if (count($applicationStages) === 0) {
            $this->line('No application stages found for which a letter should be generated');
            return;
        }

        foreach ($applicationStages as $applicationStage) {
            $this->line(sprintf('Dispatch letter generation event for %s', $applicationStage->id));

            // TODO/FIXME: This is temporal code to be able te generate letters easily from the command app:generate-pdf
            if (is_null($applicationStage->assessor_decision)) {
                /** @var ApplicationStageDecision $randomDecision */
                $randomDecision = Collection::make(ApplicationStageDecision::cases())->random();
                $applicationStage->assessor_decision = $randomDecision;
                $applicationStage->save();
            }

            ApplicationStageDecidedEvent::dispatch($applicationStage);
        }
    }

    private function getApplicationStages(): array
    {
        return
            ApplicationStage::query()
                ->whereRelation('application', 'status', '=', ApplicationStatus::Submitted)
                ->whereRelation('subsidyStage', 'stage', '=', 1)
                ->whereNull('assessor_decision')
                ->orderBy('created_at')
                ->get()
                ->all();
    }
}
