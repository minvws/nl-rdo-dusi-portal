<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class ApplicationHashService
{
    public function getSubsidyStageHashesForSubsidy(Subsidy $subsidy): Collection
    {
        return $subsidy->subsidyVersions->flatMap(
            fn(SubsidyVersion $version) => $version->subsidyStages->flatMap(
                fn(SubsidyStage $stage) => $stage->subsidyStageHashes
            )
        );
    }

    public function getApplicationHashDuplicatesQuery(SubsidyStageHash $subsidyStageHash): Builder
    {
        $query = ApplicationHash::query()->select(
            'hash',
            DB::raw('COUNT(*) as count'),
            DB::raw('string_agg(application_id::text, \',\') as application_ids')
        )->where('subsidy_stage_hash_id', $subsidyStageHash->id)->groupBy('hash')->havingRaw('COUNT(hash) > 1');

        return $query;
    }

    public function getSubsidyStageHashJoinedQuery(): Builder
    {
        return SubsidyStageHash::query()->select('subsidy_stage_hashes.id')->join(
            'subsidy_stages',
            'subsidy_stages.id',
            'subsidy_stage_hashes.subsidy_stage_id'
        )->join(
            'subsidy_versions',
            'subsidy_versions.id',
            'subsidy_stages.subsidy_version_id'
        )->join(
            'subsidies',
            'subsidies.id',
            'subsidy_versions.subsidy_id'
        );
    }
}
