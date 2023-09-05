<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Application\Events\ApplicationStageVersionDecidedEvent;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;

class GeneratePdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-pdf {--applicationVersionId=}';

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

        if ($this->option('applicationVersionId')) {
            $applicationStageVersion = ApplicationStageVersion::findOrFail($this->option('applicationVersionId'));
        } else {
            $applicationStageVersion = ApplicationStageVersion::orderBy('created_at', 'desc')->first();
        }

        if (!$applicationStageVersion) {
            $this->error('No applicationStageVersion found!');
            return;
        }

        $this->line(sprintf('Dispatch letter generation event for %s', $applicationStageVersion->id));

        ApplicationStageVersionDecidedEvent::dispatch($applicationStageVersion);
    }
}
