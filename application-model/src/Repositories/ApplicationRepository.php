<?php

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Database\Eloquent\Collection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\AnswersByApplicationStage;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswers;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationRepository
{
    public function filterApplications(ApplicationsFilter $filter): array|Collection
    {
        $query = Application::query();
        $query->when(
            isset($filter->applicationTitle),
            fn() => $query->title($filter->applicationTitle)->get() // @phpstan-ignore-line
        );
        $query->when(
            isset($filter->dateFrom),
            fn() => $query->createdAtFrom($filter->dateFrom)->get() // @phpstan-ignore-line
        );
        $query->when(
            isset($filter->dateTo),
            fn() => $query->createdAtTo($filter->dateTo)->get() // @phpstan-ignore-line
        );
        $query->when(
            isset($filter->dateLastModifiedFrom),
            fn() => $query->updatedAtFrom(
                $filter->dateLastModifiedFrom // @phpstan-ignore-line
            )->get()
        );
        $query->when(
            isset($filter->dateLastModifiedTo),
            fn() => $query->updatedAtTo(
                $filter->dateLastModifiedTo // @phpstan-ignore-line
            )->get()
        );
        $query->when(
            isset($filter->dateFinalReviewDeadlineFrom),
            fn() => $query->finalReviewDeadlineFrom(
                $filter->dateFinalReviewDeadlineFrom // @phpstan-ignore-line
            )->get()
        );
        $query->when(
            isset($filter->dateFinalReviewDeadlineTo),
            fn() => $query->finalReviewDeadlineTo(
                $filter->dateFinalReviewDeadlineTo // @phpstan-ignore-line
            )->get()
        );
        $query->when(
            isset($filter->status),
            fn() => $query->status($filter->status)->get() // @phpstan-ignore-line
        );
        $query->when(
            isset($filter->subsidy),
            fn() => $query->subsidyTitle($filter->subsidy)->get() // @phpstan-ignore-line
        );
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

    public function getAnswer(ApplicationStage $appStage, Field $field): ?Answer
    {
        $answer = Answer::query()
            ->where('application_stage_id', $appStage->id)
            ->where('field_id', $field->id)
            ->first();
        if ($answer instanceof Answer) {
            return $answer;
        }
        return null;
    }

    public function makeApplicationForIdentityAndSubsidyVersion(
        Identity $identity,
        SubsidyVersion $subsidyVersion
    ): Application {
        $application = new Application();
        $application->identity()->associate($identity);
        $application->subsidyVersion()->associate($subsidyVersion);
        return $application;
    }

    public function makeApplicationStage(Application $application, SubsidyStage $subsidyStage): ApplicationStage
    {
        $applicationStage = new ApplicationStage();
        $applicationStage->application()->associate($application);
        $applicationStage->subsidyStage()->associate($subsidyStage);
        return $applicationStage;
    }

    public function makeAnswer(ApplicationStage $appStage, Field $field): Answer
    {
        $answer = new Answer();
        $answer->field()->associate($field);
        $answer->applicationStage()->associate($appStage);
        return $answer;
    }

    public function saveApplication(Application $application): void
    {

        $application->save();
    }

    public function saveApplicationStage(ApplicationStage $applicationStage): void
    {
        $applicationStage->save();
    }

    public function saveAnswer(Answer $answer): void
    {
        $answer->save();
    }

    public function deleteAnswer(Answer $answer): void
    {
        $answer->delete();
    }

    public function getAnswersForApplicationStagesUpToIncluding(
        ApplicationStage $stage
    ): AnswersByApplicationStage {
        $stages = [];

        /** @var array<ApplicationStage> $matchingStages */
        $matchingStages =
            $stage->application->applicationStages()
                ->with('subsidyStage')
                ->where('sequence_number', '<=', $stage->sequence_number)
                ->orderBy('sequence_number')
                ->get();

        $uniqueStages = [];
        foreach ($matchingStages as $currentStage) {
            // newer "versions" of stages will overwrite previous ones
            $stageNumber = $currentStage->subsidyStage->stage;
            $uniqueStages[$stageNumber] = $currentStage;
        }

        foreach ($uniqueStages as $currentStage) {
            /** @var array<Answer> $answers */
            $answers = $currentStage->answers()->with('field')->get()->all();

            $stages[] = new ApplicationStageAnswers(
                stage: $currentStage,
                answers: $answers
            );
        }

        return new AnswersByApplicationStage(stages: $stages);
    }

    /**
     * @return array<Application>
     */
    public function getMyApplications(Identity $identity): array
    {
        return $identity->applications()->with(['subsidyVersion', 'subsidyVersion.subsidy'])->get()->all();
    }

    public function getMyApplication(Identity $identity, string $reference): ?Application
    {
        return $identity->applications()->where('reference', $reference)->first();
    }

    public function isReferenceUnique(string $applicationReference): bool
    {
        return Application::where('reference', $applicationReference)->count() === 0;
    }
}
