<?php

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\AnswersByApplicationStage;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswers;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\User\Enums\Role;
use Ramsey\Uuid\Uuid;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationRepository
{
    private function getFilteredQueryForUser(User $user): QueryBuilder
    {
        return DB::table('application_stages', 's')
            ->join('subsidy_stages as ss', function (JoinClause $join) {
                $join->on('ss.id', '=', 's.subsidy_stage_id');
            })
            ->join('subsidy_versions as sv', function (JoinClause $join) {
                $join->on('sv.id', '=', 'ss.subsidy_version_id');
            })
            ->where('s.application_id', '=', DB::raw('applications.id'))
            ->where(function (QueryBuilder $query) use ($user) {
                foreach ($user->roles as $role) {
                    $query->orWhere(function (QueryBuilder $query) use ($role, $user) {
                        $query->where('applications.status', '<>', ApplicationStatus::Draft->value);

                        // When you are implementationCoordinator, and you have the right to view all subsidies
                        if ($role->view_all_stages) {
                            // When you are implementationCoordinator, and you only have access to a specific subsidy
                            if ($role->pivot->subsidy_id !== null) {
                                $query->where('sv.subsidy_id', '=', $role->pivot->subsidy_id);
                            }
                        } else {
                            // When you have another role
                            $query->where(function (QueryBuilder $query) use ($role, $user) {
                                // Filter on the stage where there isn't an assessor yet OR where the given user did the
                                // assessment
                                $query->where(function (QueryBuilder $query) use ($user) {
                                    $query
                                        ->whereNull('s.assessor_user_id')
                                        ->orWhere('s.assessor_user_id', '=', $user->id);
                                });

                                // Check if the user role matches the required subsidy stage role
                                $query->where('ss.assessor_user_role', '=', $role->name->value);

                                // Check if the role is linked to a specific subsidy
                                if ($role->pivot->subsidy_id !== null) {
                                    $query->where('sv.subsidy_id', '=', $role->pivot->subsidy_id);
                                }
                            });
                        }
                    });
                }
            });
    }

    public function filterApplications(
        User $user,
        bool $onlyMyApplications,
        ApplicationsFilter $filter
    ): array|Collection {
        if ($user->roles->isEmpty()) {
            return [];
        }

        $query = Application::query();

        $filteredQuery = $this->getFilteredQueryForUser($user);

        if ($onlyMyApplications) {
            $filteredQuery->where('s.assessor_user_id', '=', $user->id);
        } else {
            $filteredQuery->where(function (QueryBuilder $query) use ($user) {
                $query->where('s.is_current', true);
                $query->orWhere('s.assessor_user_id', '=', $user->id);
            });
        }

        $query->whereExists($filteredQuery);

        $this->applyFilters($query, $filter);

        if ($user->hasRole(Role::LegalSpecialist)) {
            $this->filterForLegalSpecialist($query);
        }

        return $query->get();
    }

    private function applyFilters(Builder $query, ApplicationsFilter $filter): void
    {
        $filterValues = [
            'applicationTitle' => 'title',
            'reference' => 'reference',
            'dateFrom' => 'createdAtFrom',
            'dateTo' => 'createdAtTo',
            'dateLastModifiedFrom' => 'updatedAtFrom',
            'dateLastModifiedTo' => 'updatedAtTo',
            'dateFinalReviewDeadlineFrom' => 'finalReviewDeadlineFrom',
            'dateFinalReviewDeadlineTo' => 'finalReviewDeadlineTo',
            'status' => 'status',
            'subsidy' => 'subsidyCode',
            'phase' => 'phase'
        ];

        foreach ($filterValues as $filterKey => $method) {
            $query->when(
                isset($filter->$filterKey) || is_array($filter->$filterKey),
                fn() => $query->$method($filter->$filterKey)->get()
            );
        }
    }

    public function getApplication(string $appId, bool $lockForUpdate = false): ?Application
    {
        $application = Application::query()->when($lockForUpdate, fn($q) => $q->lockForUpdate())->find($appId);
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
    public function getLatestApplicationStagesUpToIncluding(ApplicationStage $stage): array
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
        $uniqueStages = $this->getLatestApplicationStagesUpToIncluding($stage);

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
        $cloneableAnswers = $source->answers->filter(fn(Answer $answer) => !$answer->field->exclude_from_clone_data);
        foreach ($cloneableAnswers as $answer) {
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

    public function getMyApplication(Identity $identity, string $reference, bool $lockForUpdate = false): ?Application
    {
        return $identity->applications()
            ->when($lockForUpdate, fn($q) => $q->lockForUpdate())
            ->where('reference', $reference)
            ->first();
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

    public function updateOrNewApplicationStageFieldHash(
        SubsidyStageHash $subsidyStageHash,
        Application $application,
        string $hash
    ): ApplicationHash {
        return ApplicationHash::updateOrCreate(
            [
                'subsidy_stage_hash_id' => $subsidyStageHash->id,
                'application_id' => $application->id
            ],
            [
                'hash' => $hash
            ]
        );
    }

    private function filterForLegalSpecialist(Builder $query): void
    {
        $query->where('status', ApplicationStatus::Rejected);
    }
}
