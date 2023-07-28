<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

readonly class ApplicationRepository
{
    public function getApplication(string $appId): Builder|array|Collection|Model|null
    {
        return Application::query()->find($appId);
    }

    public function getApplicationStage(string $applicationStageId): Builder|array|Collection|Model|null
    {
        return ApplicationStage::query()->find($applicationStageId);
    }

    public function getApplicationStageVersion(string $appStageVersionId): Builder|array|Collection|Model|null
    {
        return ApplicationStageVersion::query()->find($appStageVersionId);
    }

    public function getAnswer(ApplicationStageVersion $appStageVersion, Field $field): Model|Builder|null
    {
        return Answer::query()
            ->where('application_stage_version_id', $appStageVersion->id)
            ->where('field_id', $field->id)
            ->first();
    }

    public function makeApplicationForSubsidyVersion(SubsidyVersion $subsidyVersion): Application
    {
        $application = new Application();
        $application->subsidy_version_id = $subsidyVersion->id;
        return $application;
    }

    public function makeApplicationStage(Application $application, SubsidyStage $subsidyStage): ApplicationStage
    {
        $applicationStage = new ApplicationStage();
        $applicationStage->application()->associate($application);
        $applicationStage->subsidy_stage_id = $subsidyStage->id;
        return $applicationStage;
    }

    public function makeApplicationStageVersion(ApplicationStage $applicationStage): ApplicationStageVersion
    {
        $appStageVersion = new ApplicationStageVersion();
        $appStageVersion->applicationStage()->associate($applicationStage);
        return $appStageVersion;
    }

    public function makeAnswer(ApplicationStageVersion $appStageVersion, Field $field): Answer
    {
        $answer = new Answer();
        $answer->applicationStageVersion()->associate($appStageVersion);
        $answer->field_id = $field->id;
        return $answer;
    }

    public function saveApplication(Application $application): void
    {
        $application->save();
    }

    public function saveApplicationStageVersion(ApplicationStageVersion $appStageVersion): void
    {
        $appStageVersion->save();
    }

    public function saveApplicationStage(ApplicationStage $applicationStage): void
    {
        $applicationStage->save();
    }

    public function saveAnswer(Answer $answer): void
    {
        $answer->save();
    }
}
