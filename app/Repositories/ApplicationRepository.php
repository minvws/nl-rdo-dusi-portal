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
use Illuminate\Support\Str;

readonly class ApplicationRepository
{
    // Helper function to check if a string is a valid UUID
    private function validateUuid(string $uuid): void
    {
        if (!Str::isUuid($uuid)) {
            throw new \InvalidArgumentException('Invalid UUID');
        }
    }

    public function getApplication(string $appId): Application
    {
        $this->validateUuid($appId);
        $application = Application::query()->findOrFail($appId);
        assert($application === null || $application instanceof Application);
        return $application;
    }

    public function getApplicationStage(string $applicationStageId): ?ApplicationStage
    {
        $this->validateUuid($applicationStageId);
        $applicationStage = ApplicationStage::query()->find($applicationStageId);
        if ($applicationStage === null) {
            return null;
        }
        assert($applicationStage instanceof ApplicationStage);
        return $applicationStage;
    }

    public function getApplicationStageVersion(string $appStageVersionId): ApplicationStageVersion
    {
        $this->validateUuid($appStageVersionId);
        $appStageVersion = ApplicationStageVersion::query()->findOrFail($appStageVersionId);
        assert($appStageVersion instanceof ApplicationStageVersion);
        return $appStageVersion;
    }

    public function getAnswer(ApplicationStageVersion $appStageVersion, Field $field): ?Answer
    {
        $answer = Answer::query()
            ->where('application_stage_version_id', $appStageVersion->id)
            ->where('field_id', $field->id)->first();
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

    // ====================================================

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
