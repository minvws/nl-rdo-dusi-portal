<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Answer;
use App\Models\Application;
use App\Models\ApplicationStage;
use App\Models\ApplicationStageVersion;

use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\SubsidyStage;
use App\Shared\Models\Definition\SubsidyVersion;

use Illuminate\Database\Eloquent\ModelNotFoundException;

readonly class ApplicationRepository
{
    public function getApplication(string $applicationId): ?Application
    {
        try {
            $application = Application::query()->findOrFail($applicationId);
        } catch (ModelNotFoundException) {
            throw new \InvalidArgumentException('Application not found');
        }
        return $application;
    }

    public function getApplicationStage(string $applicationStageId): ?ApplicationStage
    {
        try {
            $applicationStage = ApplicationStage::query()->findOrFail($applicationStageId);
        } catch (ModelNotFoundException) {
            throw new \InvalidArgumentException('Application stage not found');
        }
        return $applicationStage;
    }

    public function getApplicationStageVersion(string $applicationStageVersionId): ?ApplicationStageVersion
    {
        try {
            $applicationStageVersion = ApplicationStageVersion::query()->findOrFail($applicationStageVersionId);
        } catch (ModelNotFoundException) {
            throw new \InvalidArgumentException('Application version not found');
        }
        return $applicationStageVersion;
    }

    public function getAnswer(ApplicationStageVersion $applicationStageVersion, Field $field): ?Answer
    {
        $answer =
            Answer::query()
            ->where('application_stage_version_id', '=', $applicationStageVersion->id)
            ->where('field_id', '=', $field->id)
            ->first();

        assert($answer === null || $answer instanceof Answer);
        return $answer;
    }

    // ==================================================

    public function makeApplicationForSubsidyVersion(SubsidyVersion $subsidyVersion): Application
    {
        $application = new Application();
        $application->subsidy_version_id = $subsidyVersion->id;
        return $application;
    }

    // ====================================================

    public function makeApplicationStage(Application $application, SubsidyStage $subsidyStage): ApplicationStage
    {
        $applicationStage = new ApplicationStage();
        $applicationStage->application()->associate($application);
        $applicationStage->subsidy_stage_id = $subsidyStage->id;
        return $applicationStage;
    }

    public function makeApplicationStageVersion(ApplicationStage $applicationStage): ApplicationStageVersion
    {
        $applicationStageVersion = new ApplicationStageVersion();
        $applicationStageVersion->applicationStage()->associate($applicationStage);
        return $applicationStageVersion;
    }

    public function makeAnswer(ApplicationStageVersion $applicationStageVersion, Field $field): Answer
    {
        $answer = new Answer();
        $answer->applicationStageVersion()->associate($applicationStageVersion);
        $answer->field_id = $field->id;
        return $answer;
    }

    // ====================================================

    public function saveApplication(Application $application): void
    {
        $application->save();
    }

    public function saveApplicationStageVersion(ApplicationStageVersion $applicationStageVersion): void
    {
        $applicationStageVersion->save();
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
