<?php

namespace MinVWS\DUSi\Assessment\API\Policies;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        // can view_all_stages -> readonly voor over alle rollen van 1 subsidy
    }

    public function viewAllStages(User $user, Subsidy $subsidy): bool
    {
        return $user->hasRoles(collect(Role::ImplementationCoordinator), $subsidy->id);
    }

    public function showList(User $user): bool
    {
        return $user->hasRoles(collect(Role::ImplementationCoordinator, Role::Assessor));
    }

    public function show(User $user, Application $application): bool
    {
       return $this->authorizeAssessorAndCoordinator($user, $application);
    }

    public function release(User $user, Application $application): bool
    {
        $stage = $application->currentApplicationStage;
        if ($stage === null || $stage->assessor_user_id !== $user->id) {
            return false;
        }

        return true;
    }

    public function claim(User $user, Application $application): bool
    {
        $stage = $application->currentApplicationStage;

        if ($stage === null) {
            Log::debug('No current stage found for application');

            return false;
        }

        $subsidyStage = $stage->subsidyStage;
        if ($subsidyStage->subject_role !== SubjectRole::Assessor) {
            Log::debug('Current stage is not available for Assessor');

            return false;
        }

        if ($stage->is_submitted) {
            Log::debug('Current stage is already submitted');

            return false;
        }

        if ($stage->assessor_user_id !== null) {
            Log::debug('Current stage is already assigned');

            return false;
        }

        $subsidyId = $application->subsidyVersion->subsidy_id;
        $rolesToCheck = collect([Role::ImplementationCoordinator, Role::Assessor]);

        return $user->hasRoles($rolesToCheck, $subsidyId) || $user->hasRoles($rolesToCheck);
    }

    private function authorizeAssessorAndCoordinator(User $user, Application $application): bool
    {
        $subsidyStage = $application->currentApplicationStage?->subsidyStage;

        if (!$subsidyStage) {
            Log::debug('No current subsidyStage found for application');

            return false;
        }

        if ($subsidyStage->subject_role !== SubjectRole::Assessor) {
            Log::debug('Current stage is not available for Assessor');

            return false;
        }

        if ($subsidyStage->assessor_user_role && !$user->hasRole($subsidyStage->assessor_user_role)) {
            Log::debug('Current user has not the correct role for this subsidyStage');

            return false;
        }

        $subsidyId = $application->subsidyVersion->subsidy_id;
        $rolesToCheck = collect([Role::ImplementationCoordinator, Role::Assessor]);

        return $user->hasRoles($rolesToCheck, $subsidyId) || $user->hasRoles($rolesToCheck);
    }
}
