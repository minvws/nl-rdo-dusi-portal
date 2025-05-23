<?php

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\AnswersByApplicationStage;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswers;
use MinVWS\DUSi\Shared\Application\DTO\PaginationOptions;
use MinVWS\DUSi\Shared\Application\DTO\SortOptions;
use MinVWS\DUSi\Shared\Application\Enums\ApplicationStageGrouping;
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
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
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
            ->with([
                'firstApplicationStage',
                'currentApplicationStage',
                'currentApplicationStage.subsidyStage',
                'currentApplicationStage.assessorUser',
                'subsidyVersion',
                'subsidyVersion.subsidy',
            ])
            ->whereExists($filteredQuery)
            ->when(
                value: $user->hasRole(Role::LegalSpecialist),
                callback: fn(Builder $q) => $q->where(function (Builder $q) {
                    $q->whereIn('status', [ApplicationStatus::Approved,
                        ApplicationStatus::Rejected, ApplicationStatus::Reclaimed]);
                }),
            );

        $this->hideFinished($query, $filter);

        $this->applyFilters($query, $filter);

        $this->applySort($query, $sortOptions);

        return $query;
    }

    private function applyFilters(Builder $query, ApplicationsFilter $filter): void
    {
        $filterValues = [
            'applicationTitle' => 'title',
            'reference' => 'reference',
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
                fn() => $query->$method($filter->$filterKey)
            );
        }

        $query->when(
            isset($filter->dateFrom) || isset($filter->dateTo),
            fn() => $query->whereHas(
                'firstApplicationStage',
                function (Builder $builder) use ($filter) {
                    /** @var Builder<ApplicationStage> $builder */
                    $builder->submittedAtBetween($filter->dateFrom, $filter->dateTo);
                }
            )
        );
    }

    public function getApplication(string $appId, bool $lockForUpdate = false): ?Application
    {
        $application = Application::query()->when($lockForUpdate, fn($q) => $q->lockForUpdate())->find($appId);
        if ($application instanceof Application) {
            return $application;
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
     * @param ApplicationStage $stage
     * @param ApplicationStageGrouping $applicationStageGrouping
     *
     * @return array<int, ApplicationStage> Application stages indexed by stage number.
     */
    public function getLatestApplicationStagesUpToIncluding(
        ApplicationStage $stage,
        ApplicationStageGrouping $applicationStageGrouping,
        bool $readOnly = false
    ): array {
        $query = $stage->application->applicationStages()
            ->with('subsidyStage');

        $query = match ($applicationStageGrouping) {
            ApplicationStageGrouping::ByStageNumber =>
            $query->whereRelation('subsidyStage', 'stage', '<=', $stage->subsidyStage->stage),
            ApplicationStageGrouping::BySequenceNumber =>
            $query->where('sequence_number', '<=', $stage->sequence_number),
        };

        /** @var array<ApplicationStage> $matchingStages */
        $matchingStages = $query
            ->where('sequence_number', '<=', $stage->sequence_number)
            ->where(
                fn ($query) =>
                $query
                    ->where('is_submitted', '=', true)
                    ->when(!$readOnly, fn ($query) => $query->orWhere('id', '=', $stage->id))
            )
            ->orderBy('sequence_number')
            ->get();

        $uniqueStages = [];
        foreach ($matchingStages as $currentStage) {
            $groupingKey = match ($applicationStageGrouping) {
                ApplicationStageGrouping::ByStageNumber => $currentStage->subsidyStage->stage,
                ApplicationStageGrouping::BySequenceNumber => $currentStage->sequence_number,
            };
            $uniqueStages[$groupingKey] = $currentStage;
        }

        return $uniqueStages;
    }

    /**
     * @param ApplicationStage $stage
     * @param ApplicationStageGrouping $applicationStageGrouping
     * @return AnswersByApplicationStage
     */
    public function getAnswersForApplicationStagesUpToIncluding(
        ApplicationStage $stage,
        ApplicationStageGrouping $applicationStageGrouping = ApplicationStageGrouping::ByStageNumber,
        bool $readOnly = false
    ): AnswersByApplicationStage {
        $uniqueStages = $this->getLatestApplicationStagesUpToIncluding($stage, $applicationStageGrouping, $readOnly);

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
    public function getOrderedClosedApplicationStagesForSubsidyStage(
        Application $application,
        SubsidyStage $subsidyStage
    ): array {
        return
            $application
                ->applicationStages()
                ->where('subsidy_stage_id', '=', $subsidyStage->id)
                ->where('is_current', '=', false)
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
                ->where('is_submitted', '=', true)
                ->orderBy('sequence_number', 'desc')
                ->first();
    }

    public function getLatestSubmittedOrCurrentApplicationStageForSubsidyStage(
        Application $application,
        SubsidyStage $subsidyStage
    ): ?ApplicationStage {
        return
            $application
                ->applicationStages()
                ->where('subsidy_stage_id', '=', $subsidyStage->id)
                ->where(function ($query) {
                    $query->where('is_submitted', '=', true)
                        ->orWhere('is_current', '=', true);
                })
                ->orderBy('sequence_number', 'desc')
                ->first();
    }

    public function getLatestAllocatedApplicationStage(
        Application $application
    ): ?ApplicationStage {
        return
            $application
                ->applicationStages()
                ->join(
                    'application_stage_transitions',
                    'application_stage_transitions.new_application_stage_id',
                    '=',
                    'application_stages.id'
                )
                ->where('application_stage_transitions.new_application_status', '=', ApplicationStatus::Allocated)
                ->orderBy('application_stage_transitions.created_at', 'desc')
                ->first();
    }

    /**
     * Returns all the expired application stages that have a transition with the expiration trigger.
     *
     * The expires_at is the date that the application stage expires. The user can still submit the application
     * on this date, the next day, the application stage is expired. This is why we use CarbonImmutable::yesterday()
     * to get the stages that are expired.
     *
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     * @return Collection<array-key, ApplicationStage>
     */
    public function getExpiredApplicationStages(): Collection
    {
        return
            ApplicationStage::query()
                ->where('is_current', '=', true)
                ->where('expires_at', '<=', CarbonImmutable::yesterday())
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

    public function hasOpenOrApprovedApplicationsForSubsidy(Identity $identity, Subsidy $subsidy): bool
    {
        return
            $identity
                ->applications()
                ->ofSubsidy($subsidy->id)
                ->whereNotIn('status', ApplicationStatus::NEW_APPLICATION_ALLOWED_STATUSES)
                ->exists();
    }

    public function hasApprovedApplicationForSubsidy(Identity $identity, Subsidy $subsidy): bool
    {
        return
            $identity
                ->applications()
                ->ofSubsidy($subsidy->id)
                ->whereIn('status', [ApplicationStatus::Approved, ApplicationStatus::Allocated])
                ->exists();
    }

    public function hasRejectedApplicationForSubsidy(Identity $identity, Subsidy $subsidy): bool
    {
        return
            $identity
                ->applications()
                ->ofSubsidy($subsidy->id)
                ->whereIn('status', [ApplicationStatus::Rejected, ApplicationStatus::Reclaimed])
                ->exists();
    }

    /**
     * @return Collection<array-key, Application>
     */
    public function getMyApplications(Identity $identity, ?Subsidy $subsidy = null): Collection
    {
        $query = $identity
            ->applications()
            ->with(['subsidyVersion', 'subsidyVersion.subsidy', 'lastApplicationStage'])
            ->when($subsidy, function (Builder $query, Subsidy $subsidy) {
                /** @var Builder<Application> $query */
                $query->ofSubsidy($subsidy->id);
            });

        /** @phpstan-ignore argument.type */
        $this->filterMyApplicationsQueryValidStatus($query);

        return $query
            ->orderByStatus()
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getMyApplication(Identity $identity, string $reference, bool $lockForUpdate = false): ?Application
    {
        $query = $identity
            ->applications()
            ->when($lockForUpdate, fn($q) => $q->lockForUpdate());

        /** @phpstan-ignore argument.type */
        $this->filterMyApplicationsQueryValidStatus($query);

        return $query
            ->where('reference', $reference)
            ->first();
    }

    public function getMyApplicationsThatNeededChangesCount(Identity $identity): int
    {
        return $identity->applications()
            ->where('status', ApplicationStatus::RequestForChanges)
            ->lastApplicationStageNotExpired()
            ->count();
    }

    public function deleteAnswersByStage(ApplicationStage $applicationStage): void
    {
        $this->getAnswerQuery($applicationStage)->delete();
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

    /**
     * When we search on the Application reference we don't filter on finished applications (approved/rejected).
     */
    private function hideFinished(Builder $query, ApplicationsFilter $filter): void
    {
        $query->when(
            value: !isset($filter->reference),
            callback: fn(Builder $q) => $q->where(function (Builder $q) {
                $q->whereNotIn('status', [ApplicationStatus::Approved,
                    ApplicationStatus::Rejected, ApplicationStatus::Reclaimed]);
            }),
        );
    }

    private function getAnswerQuery(
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

    /**
     * This method filters the applications that are visible for the user.
     * When application is draft and the subsidy is not valid, the application is not visible.
     * When application is in RequestForChanges and the last application stage is expired,
     * the application is not visible.
     *
     * @param HasMany<Application> $query
     * @return void
     */
    private function filterMyApplicationsQueryValidStatus(HasMany $query): void
    {
        /** @var Builder<Application> $query */
        $query->where(function (Builder $query) {
            $query
                ->orWhere(function (Builder $query) {
                    $query->where('status', ApplicationStatus::Draft)
                        ->validSubsidy();
                })
                ->orWhere(function (Builder $query) {
                    $query->where('status', ApplicationStatus::RequestForChanges)
                        ->lastApplicationStageNotExpired();
                })
                ->orWhereIn('status', [
                    ApplicationStatus::Pending,
                    ApplicationStatus::Approved,
                    ApplicationStatus::Allocated,
                    ApplicationStatus::Rejected,
                    ApplicationStatus::Reclaimed,
                ]);
        });
    }
}
