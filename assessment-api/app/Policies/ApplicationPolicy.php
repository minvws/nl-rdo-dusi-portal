<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Policies;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\Application\Models\Application;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ApplicationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(private readonly ApplicationRepository $applicationRepository)
    {
    }

    public function viewAllStages(User $user, Subsidy $subsidy): bool
    {
        return $user->hasRoleToViewAllStagesForSubsidy($subsidy);
    }

    public function viewAllStagesAndAssessor(User $user, Subsidy $subsidy): bool
    {
        return
            $this->viewAllStages($user, $subsidy) &&
            $user->hasRoleForSubsidy(Role::ImplementationCoordinator, $subsidy);
    }

    public function filterApplications(User $user): bool
    {
        return $user->hasRole([Role::Assessor, Role::InternalAuditor, Role::ImplementationCoordinator]);
    }

    public function filterAssignedApplications(User $user): bool
    {
        return $this->filterApplications($user);
    }

    public function show(User $user, Application $application): bool
    {
        return
            $application->currentApplicationStage?->assessor_user_id === $user->id ||
            $user->hasRoleToViewAllStagesForSubsidy($application->subsidyVersion->subsidy_id) ||
            $this->applicationRepository->hasApplicationBeenAssessedByUser($application, $user);
    }

    public function save(User $user, Application $application): bool
    {
        $stage = $application->currentApplicationStage;
        return $stage !== null && !$stage->is_submitted && $stage->assessor_user_id === $user->id;
    }

    public function previewTransition(User $user, Application $application): bool
    {
        return $this->save($user, $application);
    }

    public function submit(User $user, Application $application): bool
    {
        return $this->save($user, $application);
    }

    public function release(User $user, Application $application): bool
    {
        $stage = $application->currentApplicationStage;
        if ($stage === null) {
            return false;
        }

        if (is_null($stage->assessor_user_id)) {
            return false;
        }

        if (
            $user->hasRoleForSubsidy(
                Role::ImplementationCoordinator,
                $stage->subsidyStage->subsidyVersion->subsidy->id
            )
        ) {
            return true;
        }

        return $stage->assessor_user_id === $user->id;
    }

    private function validateClaim(Application $application): bool
    {
        $stage = $application->currentApplicationStage;

        if ($stage === null) {
            Log::debug('No current stage found for application', ['applicationId' => $application->id]);
            return false;
        }

        if ($stage->is_submitted) {
            Log::debug('Current stage is already submitted', ['stageId' => $stage->id]);
            return false;
        }

        if ($stage->assessor_user_id !== null) {
            Log::debug('Current stage is already assigned', ['stageId' => $stage->id]);
            return false;
        }

        $subsidyStage = $stage->subsidyStage;
        if (
            $subsidyStage->subject_role !== SubjectRole::Assessor ||
            $subsidyStage->assessor_user_role === null
        ) {
            Log::debug('Current stage is not assignable to assessor role.', [
                'stageId' => $stage->id,
                'subjectRole' => $subsidyStage->subject_role,
                'assessorUserRole' => $subsidyStage->assessor_user_role
            ]);
            return false;
        }

        return true;
    }

    public function claim(User $user, Application $application): bool
    {
        if (!$this->validateClaim($application)) {
            return false;
        }

        $currentApplicationStage = $application->currentApplicationStage;
        if ($currentApplicationStage === null) {
            return false;
        }

        $subsidyStage = $currentApplicationStage->subsidyStage;
        if ($subsidyStage->assessor_user_role === null) {
            return false;
        }
        if (
            !$user->hasRoleForSubsidy($subsidyStage->assessor_user_role, $application->subsidyVersion->subsidy_id)
        ) {
            Log::debug(
                'Current stage is not assignable to this assessor',
                ['stageId' => $currentApplicationStage->id]
            );
            return false;
        }

        // 4-ogen principe; user can't assess more than 1 stage
        $applicationStages =
            $this->applicationRepository->getLatestApplicationStagesUpToIncluding($currentApplicationStage);
        foreach ($applicationStages as $applicationStage) {
            if ($applicationStage->assessor_user_id === $user->id) {
                Log::debug('User already assessed a stage', ['stageId' => $currentApplicationStage->id]);
                return false;
            }
        }

        return true;
    }

    public function getApplicationHistory(User $user, Application $application): bool
    {
        return $this->show($user, $application);
    }

    public function getTransitionHistory(User $user, Application $application): bool
    {
        return $this->show($user, $application);
    }

    public function getLetterFromMessage(User $user, Application $application): bool
    {
        return $this->show($user, $application);
    }

    public function assign(User $user, Application $application): bool
    {
        if (!$this->validateClaim($application)) {
            return false;
        }
        return $user->hasRoleForSubsidy(
            Role::ImplementationCoordinator,
            $application->subsidyVersion->subsidy_id
        );
    }

    public function export(User $user): bool
    {
        $pczmSubsidy = Subsidy::whereCode('PCZM')->first();
        assert($pczmSubsidy !== null);

        return $user->hasRoleForSubsidy(
            Role::DataExporter,
            $pczmSubsidy->id
        );
    }
}
