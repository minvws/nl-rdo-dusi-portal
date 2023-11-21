<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use Ramsey\Uuid\Uuid;

class ListSubsidyStageHashes extends Command
{
    protected $signature = 'subsidy-stage-hash:list {subsidyId?}';

    protected $description = 'List SubsidyStage hashes for given subsidy(optional)';

      /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $subsidyId = $this->argument('subsidyId');

        $subsidyStashHashQuery = SubsidyStageHash::query()
            ->select(['subsidy_stage_hashes.id', 'subsidy_stage_hashes.name', 'subsidy_stage_hashes.description']);

        if ($subsidyId) {
            try {
                if (Uuid::isValid($subsidyId) === false) {
                    $this->error('Invalid uuid!');
                    return 1;
                }

                /** @var Subsidy $subsidy */
                $subsidy = Subsidy::findOrFail($subsidyId);
            } catch (ModelNotFoundException $e) {
                $this->error('Subsidy not found!');
                return 1;
            }

            $subsidyStashHashQuery
                ->join('subsidy_stages', 'subsidy_stages.id', 'subsidy_stage_hashes.subsidy_stage_id')
                ->join('subsidy_versions', 'subsidy_versions.id', 'subsidy_stages.subsidy_version_id')
                ->join('subsidies', 'subsidies.id', 'subsidy_versions.subsidy_id')
                ->where('subsidies.id', $subsidy->id);
        }

        $subsidyStageHashes = $subsidyStashHashQuery->get();
        if ($subsidyStageHashes->count() > 0) {
            $this->table(array_keys($subsidyStageHashes->firstOrFail()->toArray()), $subsidyStageHashes->toArray());
        } else {
            $this->info('No SubsidyStageHases found!');
        }

        return 0;
    }
}
