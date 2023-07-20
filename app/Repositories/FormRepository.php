<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Shared\Models\Definition\SubsidyStage;
use App\Shared\Models\Definition\SubsidyVersion;
use App\Shared\Models\Definition\Field;



use Illuminate\Support\Collection;

class FormRepository
{
    public function getSubsidyVersion(string $subsidyVersionId)
    {
        $subsidyVersion = SubsidyVersion::query()->find($subsidyVersionId);
        if ($subsidyVersion instanceof SubsidyVersion === false) {
            throw new \InvalidArgumentException('Subsidy version not found');
        }
        return $subsidyVersion;
    }

    public function getSubsidyStage(string $subsidyStageId)
    {
        $subsidyStage = SubsidyStage::query()->find($subsidyStageId);
        if ($subsidyStage instanceof SubsidyStage === false) {
            throw new \InvalidArgumentException('Subsidy stage not found');
        }
        return $subsidyStage;
    }

    public function getField(SubsidyStage $subsidyStage, string $fieldCode): ?Field
    {
        $field = $subsidyStage->fields()->find($fieldCode);
        if ($field instanceof Field === false) {
            throw new \InvalidArgumentException('Field not found');
        }
        return $field;
    }

    public function getFields(SubsidyStage $subsidyStage): Collection
    {
        return $subsidyStage->fields;
    }
}
