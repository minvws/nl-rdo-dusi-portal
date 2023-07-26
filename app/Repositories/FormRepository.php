<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Shared\Models\Definition\SubsidyStage;
use App\Shared\Models\Definition\SubsidyVersion;
use App\Shared\Models\Definition\Field;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as Col;

class FormRepository
{
    public function getSubsidyVersion(string $subsidyVersionId): Builder|array|Col|Model
    {
        return SubsidyVersion::query()->find($subsidyVersionId); // @phpstan-ignore-line
    }

    public function getSubsidyStage(string $subsidyStageId): Builder|array|Col|Model
    {
        return SubsidyStage::query()->find($subsidyStageId); // @phpstan-ignore-line
    }

    public function getField(SubsidyStage $subsidyStage, string $fieldCode): ?Field
    {
        return $subsidyStage->fields()->where('code', '=', $fieldCode)->first();
    }

    public function getFields(SubsidyStage $subsidyStage): Collection
    {
        return $subsidyStage->fields;
    }
}
