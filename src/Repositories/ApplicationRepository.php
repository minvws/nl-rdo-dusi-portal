<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

readonly class ApplicationRepository
{
    public function getApplication(string $appId): ?Application
    {
        $application = Application::find($appId); // @phpstan-ignore-line
        if ($application instanceof Application) {
            return $application;
        }
        return null;
    }

    public function getApplicationStage(string $applicationStageId): ?ApplicationStage
    {
        $applicationStage = ApplicationStage::find($applicationStageId); // @phpstan-ignore-line
        if ($applicationStage instanceof ApplicationStage) {
            return $applicationStage;
        }
        return null;
    }

    public function getApplicationStageVersions(ApplicationStage $applicationStage): mixed
    {
        return ApplicationStageVersion::query()
            ->where('application_stage_id', $applicationStage->id)
            ->orderBy('version', 'asc')
            ->get();
    }

    public function getLatestApplicationStageVersion(ApplicationStage $applicationStage): ?ApplicationStageVersion
    {
        $latestApplicationStageVersion = ApplicationStageVersion::query()
            ->where('application_stage_id', $applicationStage->id)
            ->orderBy('version', 'desc')
            ->first();
        if ($latestApplicationStageVersion instanceof ApplicationStageVersion) {
            return $latestApplicationStageVersion;
        }
        return null;
    }
    public function getApplicationStageVersion(string $appStageVersionId): ?ApplicationStageVersion
    {
        $applicationStageVersion = ApplicationStageVersion::find($appStageVersionId); // @phpstan-ignore-line
        if ($applicationStageVersion instanceof ApplicationStageVersion) {
            return $applicationStageVersion;
        }
        return null;
    }

    public function getAnswer(ApplicationStageVersion $appStageVersion, Field $field): ?Answer
    {
        $answer = Answer::query()
            ->where('application_stage_version_id', $appStageVersion->id)
            ->where('field_id', $field->id)
            ->first();
        if ($answer instanceof Answer) {
            return $answer;
        }
        return null;
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

    public function makeApplicationStageVersion(
        ApplicationStage $applicationStage
    ): ApplicationStageVersion {
        $applicationStageVersion = new ApplicationStageVersion([
            'status' => ApplicationStageVersionStatus::Draft->value,
        ]);
        $applicationStageVersion->applicationStage()->associate($applicationStage);
        return $applicationStageVersion;
    }

    public function makeAnswer(ApplicationStageVersion $appStageVersion, Field $field): Answer
    {
        $answer = new Answer([
            'field_id' => $field->id,
        ]);
        $answer->applicationStageVersion()->associate($appStageVersion);
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
