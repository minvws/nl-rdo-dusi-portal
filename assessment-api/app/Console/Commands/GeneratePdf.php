<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Application\Events\ApplicationStageDecidedEvent;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

class GeneratePdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-pdf {--applicationStageId=}';

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
        if (!app()->environment('local')) {
            throw new \RuntimeException('This command can only be use locally');
        }

        if ($this->option('applicationStageId')) {
            $applicationStage = ApplicationStage::findOrFail($this->option('applicationStageId'));
        } else {
            $applicationStage = ApplicationStage::orderBy('created_at', 'desc')->first();
        }

        if (!$applicationStage) {
            $this->error('No application stage found!');
            return;
        }

        $this->line(sprintf('Dispatch letter generation event for %s', $applicationStage->id));

        ApplicationStageDecidedEvent::dispatch($applicationStage);
    }
}
