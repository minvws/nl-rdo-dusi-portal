<?php

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ApplicationRepository
{
    public function filterApplications(ApplicationsFilter $filter): array|Collection
    {
        $query = Application::query();

        if (isset($filter->applicationTitle)) {
             $query->title($filter->applicationTitle);
        }

//        $query->when(
//            isset($filter->dateFrom),
//            fn () => $query->createdAtFrom($filter->dateFrom)->get()
//        );
//        $query->when(
//            isset($filter->dateTo),
//            fn () =>$query->createdAtTo($filter->dateTo)->get()
//        );
//        $query->when(
//            isset($filter->dateLastModifiedFrom),
//            fn () =>$query->updatedAtFrom(
//                $filter->dateLastModifiedFrom
//            )->get()
//        );
//        $query->when(
//            isset($filter->dateLastModifiedTo),
//            fn () =>$query->updatedAtTo(
//                $filter->dateLastModifiedTo
//            )->get()
//        );
//        $query->when(
//            isset($filter->dateFinalReviewDeadlineFrom),
//            fn () =>$query->finalReviewDeadlineFrom(
//                $filter->dateFinalReviewDeadlineFrom
//            )->get()
//        );
//        $query->when(
//            isset($filter->dateFinalReviewDeadlineTo),
//            fn () =>$query->finalReviewDeadlineTo(
//                $filter->dateFinalReviewDeadlineTo
//            )->get()
//        );
//        $query->when(
//            isset($filter->status),
//            fn () =>$query->status($filter->status)->get()
//        );
//        $query->when(
//            isset($filter->subsidy),
//            fn () =>$query->subsidyTitle($filter->subsidy)->get()
//        );
        return $query->get();
    }

    public function getApplication(string $appId): ?Application
    {
        $application = Application::find($appId);
        if ($application instanceof Application) {
            return $application;
        }
        return null;
    }

    public function getApplicationStage(string $applicationStageId): ?ApplicationStage
    {
        $applicationStage = ApplicationStage::find($applicationStageId);
        if ($applicationStage instanceof ApplicationStage) {
            return $applicationStage;
        }
        return null;
    }


    /*
     * @param ApplicationStage $applicationStage
     * @return \Illuminate\Database\Eloquent\Collection<ApplicationStageVersion>
     */
    public function getApplicationStageVersions(
        ApplicationStage $applicationStage
    ): \Illuminate\Database\Eloquent\Collection {
        return ApplicationStageVersion::query()
            ->where('application_stage_id', $applicationStage->id)
            ->orderBy('version', 'desc')
            ->get()
            ->filter(fn (ApplicationStageVersion $appStageVersion)
            => $appStageVersion instanceof ApplicationStageVersion);
    }

    /*
     * @param ApplicationStage $applicationStage
     * @return ApplicationStageVersion|null
     */
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
        $applicationStageVersion = ApplicationStageVersion::find($appStageVersionId);
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
