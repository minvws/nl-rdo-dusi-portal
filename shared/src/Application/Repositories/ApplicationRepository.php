<?php

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\AnswersByApplicationStage;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswers;
use MinVWS\DUSi\Shared\Application\DTO\PaginationOptions;
use MinVWS\DUSi\Shared\Application\DTO\SortOptions;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;
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
use RuntimeException;

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
                        $query
                            ->where('applications.status', '<>', ApplicationStatus::Draft->value)
                            // When your role is limited to a specific subsidy
                            ->when(
                                value: $role->pivot->subsidy_id !== null,
                                callback: fn($q) => $q->where('sv.subsidy_id', '=', $role->pivot->subsidy_id)
                            );

                        // When you can view all stages, it does not matter if there is an assessor attached
                        if ($role->view_all_stages) {
                            return;
                        }

                        // When you have another role
                        $query->where(function (QueryBuilder $query) use ($role, $user) {
                            // Filter on the stage where there isn't an assessor yet OR where the given user did the
                            // assessment
                            $query->where('s.assessor_user_id', '=', $user->id);
                            $query->orWhere(function (QueryBuilder $query) use ($role) {
                                $query
                                    // Check if the current stage is not yet assessed
                                    ->where('s.is_current', true)
                                    ->whereNull('s.assessor_user_id')
                                    // Then also the user role need to match the required subsidy stage role
                                    ->where('ss.assessor_user_role', '=', $role->name->value);
                            });
                        });
                    });
                }
            });
    }

    public function filterApplicationsPaginated(
        User $user,
        bool $onlyMyApplications,
        ApplicationsFilter $filter,
        PaginationOptions $paginationOptions,
        SortOptions $sortOptions,
    ): LengthAwarePaginatorContract {
        if ($user->roles->isEmpty()) {
            return new LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: $paginationOptions->getPerPage(),
                currentPage: $paginationOptions->getPage(),
            );
        }

        return $this->filterApplicationsQuery(
            user: $user,
            onlyMyApplications: $onlyMyApplications,
            filter: $filter,
            sortOptions: $sortOptions,
        )->paginate(perPage: $paginationOptions->getPerPage(), page: $paginationOptions->getPage());
    }

    protected function filterApplicationsQuery(
        User $user,
        bool $onlyMyApplications,
        ApplicationsFilter $filter,
        SortOptions $sortOptions,
    ): Builder {
        if ($user->roles->isEmpty()) {
            throw new RuntimeException('Not possible to filter applications when user has no roles');
        }

        $filteredQuery = $this->getFilteredQueryForUser($user);

        if ($onlyMyApplications) {
            $filteredQuery->where('s.assessor_user_id', '=', $user->id);
        }

        $query = Application::query()
            ->whereExists($filteredQuery)
            ->when(
                value: $user->hasRole(Role::LegalSpecialist),
                callback: fn($q) => $q->where('status', ApplicationStatus::Rejected),
            );
        $this->applyFilters($query, $filter);
        $this->applySort($query, $sortOptions);

        return $query;
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
                ->where(
                    fn ($query) =>
                        $query
                            ->where('is_submitted', '=', true)
                            ->orWhere('id', '=', $stage->id)
                )
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

    public function getCurrentApplicantApplicationStage(
        Application $application,
        bool $includeAnswers
    ): ?ApplicationStage {
        $query =
            $application
                ->applicationStages()
                ->whereRelation('subsidyStage', 'stage', '=', 1)
                ->whereRelation('subsidyStage', 'subject_role', '=', SubjectRole::Applicant)
                ->where(
                    fn ($query) =>
                        $query
                            ->where('is_current', '=', true)
                            ->orWhere('is_submitted', '=', true)
                )
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
    public function getOrderedSubmittedApplicationStagesForSubsidyStage(
        Application $application,
        SubsidyStage $subsidyStage
    ): array {
        return
            $application
                ->applicationStages()
                ->where('subsidy_stage_id', '=', $subsidyStage->id)
                ->where('is_submitted', '=', true)
                ->orderBy('sequence_number')
                ->get()
                ->all();
    }

    public function getLatestSubmittedApplicationStageForSubsidyStage(
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

    /**
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     * @return Collection<int, ApplicationStage>
     */
    public function getExpiredApplicationStages(): Collection
    {
        return
            ApplicationStage::query()
                ->where('is_current', '=', true)
                ->where('expires_at', '<=', CarbonImmutable::now())
                ->whereExists(
                    SubsidyStageTransition::query()
                        ->select(DB::raw(1))
                        ->whereColumn(
                            'current_subsidy_stage_id',
                            '=',
                            'application_stages.subsidy_stage_id'
                        )
                        ->where('evaluation_trigger', '=', EvaluationTrigger::Expiration)
                )
                ->get();
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

    public function getMyApplicationsThatNeededChangesCount(Identity $identity): int
    {
        return $identity->applications()
            ->where('status', ApplicationStatus::RequestForChanges)
            ->count();
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

    private function applySort(Builder $query, SortOptions $sortOptions): void
    {
        foreach ($sortOptions->getSortColumns() as $column) {
            $query->orderBy($column->getColumn(), $column->getDirection());
        }
    }
}
