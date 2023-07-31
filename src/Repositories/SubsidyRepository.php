<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageUI;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class SubsidyRepository
{
    public function getActiveSubsidies(): \Illuminate\Support\Collection
    {
 //@phpstan-ignore-next-line
        return Subsidy::query()->active()->ordered()->with('publishedVersion.subsidyStages')->get();
    }

    public function getSubsidiesWithSubsidyStagesForSubjectRole(SubjectRole $subjectRole): \Illuminate\Support\Collection
    {
        return Subsidy::query()->subjectRole($subjectRole)->ordered()->with('publishedVersion.subsidyStages')->get();
    }

    public function getSubsidy(string $id): Model|Collection|Builder|array|null
    {
        return Subsidy::query()->with('subsidyVersions.subsidyStages')->find($id);
    }

    public function getSubsidyStage(string $id): Model|Collection|Builder|array|null
    {
        return SubsidyStage::query()->find($id);
    }

    public function getField(string $id): Model|Collection|Builder|array|null
    {
        return Field::query()->find($id);
    }

    public function makeSubsidy(): Subsidy
    {
        return new Subsidy();
    }

    public function saveSubsidy(Subsidy $subsidy): void
    {
        $subsidy->save();
    }

    public function makeSubsidyVersion(Subsidy $subsidy): SubsidyVersion
    {
        $subsidyVersion = new SubsidyVersion();
        $subsidyVersion->subsidy()->associate($subsidy);
        return $subsidyVersion;
    }

    public function makeSubsidyStage(SubsidyVersion $subsidyVersion): SubsidyStage
    {
        $subsidyStage = new SubsidyStage();
        $subsidyStage->subsidyVersion()->associate($subsidyVersion);
        return $subsidyStage;
    }

    public function saveSubsidyStage(SubsidyStage $subsidyStage): void
    {
        $subsidyStage->save();
    }

    public function makeSubsidyStageUI(SubsidyStage $subsidyStage): SubsidyStageUI
    {
        $subsidyStageUI = new SubsidyStageUI();
        $subsidyStageUI->subsidyStage()->associate($subsidyStage);
        return $subsidyStageUI;
    }

    public function saveSubsidyStageUI(SubsidyStageUI $subsidyStageUI): void
    {
        $subsidyStageUI->save();
    }

    public function makeField(SubsidyStage $subsidyStage): Field
    {
        $field = new Field();
        $field->subsidyStages()->attach($subsidyStage);
        return $field;
    }

    public function saveField(Field $field): void
    {
        $field->save();
    }
}
