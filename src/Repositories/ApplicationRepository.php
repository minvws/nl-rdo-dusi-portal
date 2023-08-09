<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use DateTime;
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

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
readonly class ApplicationRepository
{
    public function queryApplicationWithTitle(string $title): Builder
    {
        $query = Application::query();
        return $query->title($title); // @phpstan-ignore-line
    }
    public function queryApplicationWithCreatedAtFrom(DateTime $createdAt): Builder
    {
        $query = Application::query();
        return $query->createdAtFrom($createdAt); // @phpstan-ignore-line
    }

    public function queryApplicationWithCreatedAtTo(DateTime $createdAt): Builder
    {
        $query = Application::query();
        return $query->createdAtTo($createdAt); // @phpstan-ignore-line
    }

    public function queryApplicationWithUpdatedAtFrom(DateTime $updatedAt): Builder
    {
        $query = Application::query();
        return $query->updatedAtFrom($updatedAt); // @phpstan-ignore-line
    }

    public function queryApplicationWithUpdatedAtTo(DateTime $updatedAt): Builder
    {
        $query = Application::query();
        return $query->updatedAtTo($updatedAt); // @phpstan-ignore-line
    }

    public function queryApplicationWithFinalReviewDeadlineFrom(DateTime $finalReviewDeadline): Builder
    {
        $query = Application::query();
        return $query->finalReviewDeadlineFrom($finalReviewDeadline); // @phpstan-ignore-line
    }

    public function queryApplicationWithFinalReviewDeadlineTo(DateTime $finalReviewDeadline): Builder
    {
        $query = Application::query();
        return $query->finalReviewDeadlineTo($finalReviewDeadline); // @phpstan-ignore-line
    }

    public function queryApplicationWithStatus(ApplicationStageVersionStatus $status): Builder
    {
        $query = Application::query();
        return $query->status($status); // @phpstan-ignore-line
    }

    public function queryApplicationWithSubsidyTitle(string $title): Builder
    {
        $query = Application::query();
        return $query->subsidyTitle($title); // @phpstan-ignore-line
    }

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

    public function getApplicationStageVersions(ApplicationStage $applicationStage): Collection
    {
        return ApplicationStageVersion::query()
            ->where('application_stage_id', $applicationStage->id)
            ->orderBy('version', 'desc')
            ->get();
    }

    public function getLatestApplicationStageVersion(ApplicationStage $applicationStage): ?ApplicationStageVersion
    {
        $latestApplicationStageVersion = ApplicationStageVersion::query()
            ->where('application_stage_id', $applicationStage->id)
            ->orderBy('version', 'asc')
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
