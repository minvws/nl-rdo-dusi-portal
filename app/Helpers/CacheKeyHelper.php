<?php
declare(strict_types=1);

namespace App\Helpers;

use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;

class CacheKeyHelper
{
    public function keyForActiveSubsidies(): string
    {
        return 'subsidy_list';
    }

    public function keyForSubsidyStageId(string $id): string
    {
        return 'subsidy_stage_' . $id;
    }

    public function keyForSubsidyStage(SubsidyStage $subsidyStage): string
    {
        return $this->keyForSubsidyStageId($subsidyStage->id);
    }
}
