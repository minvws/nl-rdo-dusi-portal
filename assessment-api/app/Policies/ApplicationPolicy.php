<?php

namespace MinVWS\DUSi\Assessment\API\Policies;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
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

    }

    public function claim(User $user, Application $application): bool
    {

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
