<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class SubsidyStageHashUpdateCommand extends Command
{
    protected $signature = 'subsidy-stage-hash:update {subsidyStageHashId}';

    protected $description = 'Update application hashes for given subsidy-stage-hash';

    public function __construct(private readonly ApplicationDataService $applicationDataService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $subsidyStageHashId = $this->argument('subsidyStageHashId');

        try {
            if (Uuid::isValid($subsidyStageHashId) === false) {
                $this->error('Invalid uuid!');
                return 1;
            }

            /** @var SubsidyStageHash $subsidyStageHash */
            $subsidyStageHash = SubsidyStageHash::findOrFail($subsidyStageHashId);
        } catch (ModelNotFoundException $e) {
            $this->error('SubsidyStageHash not found!');
            return 1;
        }

        Assert::notNull($subsidyStageHash->subsidyStage);
        $applicationStages = ApplicationStage::where('subsidy_stage_id', $subsidyStageHash->subsidyStage->id)
            ->orderBy('id');

        $total = $applicationStages->count();

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat('verbose');

        $applicationStages->chunk(200, function ($applicationStages) use ($subsidyStageHash, $bar) {
            foreach ($applicationStages as $applicationStage) {
                $applicationStageDataAsFieldValues =
                    $this->applicationDataService->getApplicationStageDataAsFieldValues($applicationStage);

                $this->applicationDataService->updateSubsidyStageHash(
                    $applicationStageDataAsFieldValues,
                    $subsidyStageHash,
                    $applicationStage
                );

                $bar->advance();
            }
        });

        $bar->finish();

        $this->info('');

        $this->info('Operation completed!');

        return 0;
    }
}
