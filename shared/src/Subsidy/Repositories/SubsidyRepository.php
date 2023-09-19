<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Repositories;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class SubsidyRepository
{
    /*
     * @return Collection<Subsidy>
     */
    public function getActiveSubsidies(): Collection
    {
        /** @phpstan-ignore-next-line */
        return Subsidy::query()->active()->ordered()->with('publishedVersion.subsidyStages')->get();
    }

    /*
     * @param SubjectRole $subjectRole
     * @return Collection<Subsidy>
     */
    public function getSubsidiesWithSubsidyStagesForSubjectRole(SubjectRole $subjectRole): Collection
    {
        /** @phpstan-ignore-next-line */
        return Subsidy::query()->subjectRole($subjectRole)->ordered()->with('publishedVersion.subsidyStages')->get();
    }

    /*
     * @param string $id
     * @return ?Subsidy
     */
    public function getSubsidy(string $id): ?Subsidy
    {
        $subsidy = Subsidy::query()->with('subsidyVersions.subsidyStages')->find($id);
        if ($subsidy instanceof Subsidy) {
            return $subsidy;
        }
        return null;
    }

    /*
     * @param string $id
     * @return ?SubsidyStage
     */
    public function getSubsidyStage(string $id): ?SubsidyStage
    {
        $subsidyStage = SubsidyStage::find($id);
        if ($subsidyStage instanceof SubsidyStage) {
            return $subsidyStage;
        }
        return null;
    }

    /*
     * @param string $id
     * @return ?SubsidyStageUI
     */
    public function getSubsidyVersion(string $id): ?SubsidyVersion
    {
        $subsidyVersion = SubsidyVersion::find($id);
        if ($subsidyVersion instanceof SubsidyVersion) {
            return $subsidyVersion;
        }
        return null;
    }

    /*
     * @param string $id
     * @return ?Field
     */
    public function getField(string $id): ?Field
    {
        $field = Field::find($id);
        if ($field instanceof Field) {
            return $field;
        }
        return null;
    }

    /*
     * @param SubsidyStage $subsidyStage
     * @param string $code
     * @return ?Field
     */
    public function getFieldForSubsidyStageAndCode(SubsidyStage $subsidyStage, string $code): ?Field
    {
        $field = Field
            ::where('subsidy_stage_id', $subsidyStage->id)
            ->where('code', $code)
            ->first();
        if ($field instanceof Field) {
            return $field;
        }
        return null;
    }

    /*
     * @param SubsidyStage $subsidyStage
     * @return Collection<Field>
     */
    public function getFields(SubsidyStage $subsidyStage): Collection
    {
        return Field
            ::where('subsidy_stage_id', $subsidyStage->id)
            ->get();
    }

    /*
     * @return Subsidy
     */
    public function makeSubsidy(): Subsidy
    {
        return new Subsidy();
    }

    /*
     * @param Subsidy $subsidy
     */
    public function saveSubsidy(Subsidy $subsidy): void
    {
        $subsidy->save();
    }

    /*
     * @param Subsidy $subsidy
     * @return SubsidyVersion
     */
    public function makeSubsidyVersion(Subsidy $subsidy): SubsidyVersion
    {
        $subsidyVersion = new SubsidyVersion();
        $subsidyVersion->subsidy()->associate($subsidy);
        return $subsidyVersion;
    }

    /*
     * @param SubsidyVersion $subsidyVersion
     * @return SubsidyStage
     */
    public function makeSubsidyStage(SubsidyVersion $subsidyVersion): SubsidyStage
    {
        $subsidyStage = new SubsidyStage();
        $subsidyStage->subsidyVersion()->associate($subsidyVersion);
        return $subsidyStage;
    }

    /*
     * @param SubsidyStage $subsidyStage
     */
    public function saveSubsidyStage(SubsidyStage $subsidyStage): void
    {
        $subsidyStage->save();
    }

    /*
     * @param SubsidyStage $subsidyStage
     * @return Field
     */
    public function makeField(SubsidyStage $subsidyStage): Field
    {
        $field = new Field();
        $field->subsidyStage()->associate($subsidyStage);
        return $field;
    }

    /*
     * @param Field $field
     */
    public function saveField(Field $field): void
    {
        $field->save();
    }

    /*
     * @param Subsidy $subsidy
     * @return SubsidyVersion
     */
    public function makeSubsidyLetter(SubsidyVersion $subsidyVersion): SubsidyStageTransitionMessage
    {
        $subsidyLetter = new SubsidyStageTransitionMessage();
        $subsidyLetter->subsidyVersion()->associate($subsidyVersion);

        return $subsidyLetter;
    }

    /*
     * @param SubsidyLetter $subsidyLetter
     * @return Collection<string>
     */
    public function getActiveSubsidyCodes(): Collection
    {
        return Subsidy::query()->active()->ordered()->pluck('code');
    }

    public function findSubsidyByCode(string $code): ?Subsidy
    {
        return Subsidy::query()->where('code', '=', $code)->first();
    }

    public function getFirstStageForSubsidyVersion(SubsidyVersion $subsidyVersion): SubsidyStage
    {
        $stage = $subsidyVersion->subsidyStages()->where('stage', '=', 1)->first();
        assert($stage instanceof SubsidyStage);
        return $stage;
    }
}
