<?php

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\AnswersByApplicationStage;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswers;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\User\Models\User;
use Ramsey\Uuid\Uuid;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationRepository
{
    private function filterForUser(Builder $query, User $user): void
    {
        $clauses = [];
        $bindings = [];
        foreach ($user->roles as $role) {
            if ($role->view_all_stages && $role->pivot->subsidy_id === null) {
                $clauses[] = '(1 = 1)';
            } elseif ($role->view_all_stages) {
                $clauses[] = '(sv.subsidy_id = ?)';
                $bindings[] = $role->pivot->subsidy_id;
            } else {
                $clause = '((s.assessor_user_id IS NULL OR s.assessor_user_id = ?) AND ss.assessor_user_role = ?)';
                $bindings[] = $user->id;
                $bindings[] = $role->name->value;

                if ($role->pivot->subsidy_id !== null) {
                    $clause = '(' . $clause . ' AND sv.subsidy_id = ?)';
                    $bindings[] = $role->pivot->subsidy_id;
                }

                $clauses[] = $clause;
            }
        }

        $sql = "
            EXISTS (
                SELECT 1
                FROM application_stages s
                JOIN subsidy_stages ss ON (ss.id = s.subsidy_stage_id)
                JOIN subsidy_versions sv ON (sv.id = ss.subsidy_version_id)
                WHERE s.application_id = applications.id
                AND s.is_current = true
                AND (
                    (" . implode(") OR (", $clauses) . ")
                )
            )
        ";

        $query->whereRaw($sql, $bindings);
    }

    public function filterApplications(User $user, bool $onlyMyApplications, ApplicationsFilter $filter): array|Collection
    {
        if ($user->roles->isEmpty()) {
            return [];
        }

        $query = Application::query();
        $this->filterForUser($query, $user);

        if ($onlyMyApplications) {
            $this->selectAssignedAndHandledApplications($query, $user);
        }
        $query->when(
            isset($filter->applicationTitle),
            fn() => $query->title($filter->applicationTitle)->get() // @phpstan-ignore-line
        );
        $query->when(
            isset($filter->reference),
            fn() => $query->reference($filter->reference)->get() // @phpstan-ignore-line
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
            (isset($filter->status) && count($filter->status) > 0),
            fn() => $query->status($filter->status)->get() // @phpstan-ignore-line
        );
        $query->when(
            (isset($filter->subsidy) && count($filter->subsidy) > 0),
            fn() => $query->subsidyCode($filter->subsidy)->get() // @phpstan-ignore-line
        );
        $query->when(
            (isset($filter->phase) && count($filter->phase) > 0),
            fn() => $query->phase($filter->phase)->get() // @phpstan-ignore-line
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
        $answer = $this->getAnswerQuery($appStage, $field)->first();
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

    /**
     * Returns a list of application stages up to (and including) the given stage. If an application
     * has gone through certain stages multiple times only the latest instance of the stages
     * will be returned.
     *
     * @return array<int, ApplicationStage> Application stages indexed by stage number.
     */
    public function getApplicationStagesUpToIncluding(ApplicationStage $stage): array
    {
        /** @var array<ApplicationStage> $matchingStages */
        $matchingStages =
            $stage->application->applicationStages()
                ->with('subsidyStage')
                ->where('sequence_number', '<=', $stage->sequence_number)
                ->whereRelation('subsidyStage', 'stage', '<=', $stage->subsidyStage->stage)
                ->orderBy('sequence_number')
                ->get();

        $uniqueStages = [];
        foreach ($matchingStages as $currentStage) {
            // newer "versions" of stages will overwrite previous ones
            $stageNumber = $currentStage->subsidyStage->stage;
            $uniqueStages[$stageNumber] = $currentStage;
        }

        return $uniqueStages;
    }

    public function getAnswersForApplicationStagesUpToIncluding(
        ApplicationStage $stage
    ): AnswersByApplicationStage {
        $uniqueStages = $this->getApplicationStagesUpToIncluding($stage);

        $stages = [];
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

    public function getApplicantApplicationStage(Application $application, bool $includeAnswers): ?ApplicationStage
    {
        $query =
            $application
                ->applicationStages()
                ->whereRelation('subsidyStage', 'stage', '=', 1)
                ->whereRelation('subsidyStage', 'subject_role', '=', SubjectRole::Applicant)
                ->orderBy('sequence_number', 'desc')
                ->limit(1);

        if ($includeAnswers) {
            $query->with(['answers', 'answers.field']);
        }

        return $query->first();
    }

    /**
     * @return array<ApplicationStage>
     */
    public function getOrderedApplicationStagesForSubsidyStage(
        Application $application,
        SubsidyStage $subsidyStage
    ): array {
        return
            $application
                ->applicationStages()
                ->where('subsidy_stage_id', '=', $subsidyStage->id)
                ->orderBy('sequence_number')
                ->get()
                ->all();
    }

    public function getLatestApplicationStageForSubsidyStage(
        Application $application,
        SubsidyStage $subsidyStage
    ): ?ApplicationStage {
        return
            $application
                ->applicationStages()
                ->where('subsidy_stage_id', '=', $subsidyStage->id)
                ->orderBy('sequence_number', 'desc')
                ->first();
    }

    public function cloneApplicationStageAnswers(ApplicationStage $source, ApplicationStage $target): void
    {
        foreach ($source->answers as $answer) {
            $newAnswer = $answer->replicate(['application_stage_id']);
            $newAnswer->applicationStage()->associate($target);
            $newAnswer->save();
        }
    }

    public function findMyApplicationForSubsidy(Identity $identity, Subsidy $subsidy): ?Application
    {
        return
            $identity
                ->applications()
                ->whereRelation('subsidyVersion', 'subsidy_id', '=', $subsidy->id)
                ->orderBy('created_at', 'desc')
                ->first();
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

    public function deleteAnswersByStage(ApplicationStage $applicationStage): void
    {
        $this->getAnswerQuery($applicationStage)->delete();
    }

    protected function getAnswerQuery(
        ApplicationStage $applicationStage,
        ?Field $field = null,
    ): Builder {
        return Answer::query()
            ->where('application_stage_id', $applicationStage->id)
            ->when(
                $field !== null,
                function (Builder $query) use ($field) {
                    // Assertion is above in the value parameter
                    assert($field instanceof Field);
                    $query->where('field_id', $field->id);
                }
            );
    }

    public function assignApplicationStage(ApplicationStage $applicationStage, ?User $user): void
    {
        $applicationStage->assessorUser()->associate($user);
        $applicationStage->save();
    }

    public function hasApplicationBeenAssessedByUser(Application $application, User $user): bool
    {
        return Application::query()
                ->join('application_stages', 'application_stages.application_id', 'applications.id')
                ->where('application_stages.assessor_user_id', $user->id)
                ->where('applications.id', $application->id)
                ->count() > 0;
    }

    public function createApplicationStageTransition(
        SubsidyStageTransition $subsidyStageTransition,
        Application $application,
        ApplicationStage $previousApplicationStage,
        ApplicationStatus $previousApplicationStatus,
        ?ApplicationStage $newApplicationStage,
        ApplicationStatus $newApplicationStatus
    ): ApplicationStageTransition {
        $transition = new ApplicationStageTransition();
        $transition->id = Uuid::uuid4()->toString();
        $transition->subsidyStageTransition()->associate($subsidyStageTransition);
        $transition->application()->associate($application);
        $transition->previousApplicationStage()->associate($previousApplicationStage);
        $transition->previous_application_status = $previousApplicationStatus;
        $transition->newApplicationStage()->associate($newApplicationStage);
        $transition->new_application_status = $newApplicationStatus;
        $transition->save();
        return $transition;
    }

    public function getApplicationStageAnswerForField(ApplicationStage $applicationStage, Field $field): Answer|null
    {
        $answer = $applicationStage->answers()->firstWhere('field_id', $field->id);

        return $answer ?? null;
    }

    public function selectAssignedAndHandledApplications(Builder $query, User $user): void
    {
        $query->whereRelation(
            'applicationStages',
            'assessor_user_id',
            $user->id
        );
    }
}
