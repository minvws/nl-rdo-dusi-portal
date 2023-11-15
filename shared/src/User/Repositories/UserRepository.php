<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Repositories;

use Illuminate\Database\Eloquent\Collection;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\User\Models\User;
use Ramsey\Uuid\Uuid;
use MinVWS\DUSi\Shared\User\Enums\Role;

class UserRepository
{
    public function find(
        string $id
    ): User | null {
        return User::find($id);
    }

    /*
     * Get all users that are not already assessors for this subsidy stage
     * @param Role $role
     * @param SubsidyStage $subsidyStage
     * @param array<string> $previousAssessors
     * @param string|null $search
     * @return Collection<User>
     */
    public function getPotentialUsersWithSpecificRole(
        SubsidyStage $subsidyStage,
        array $previousAssessors,
        string|null $search
    ): Collection {
        $subsidyId = $subsidyStage->subsidyVersion->subsidy_id;
        $assessorRole = $subsidyStage->assessor_user_role;

        return User::whereNotIn('id', $previousAssessors)
            ->whereHas('roles', function ($query) use ($subsidyId, $assessorRole) {
                $query
                    ->where(function ($query) use ($subsidyId) {
                        $query
                            ->whereNull('subsidy_id')
                            ->orWhere('subsidy_id', $subsidyId);
                    })
                    ->where('name', $assessorRole);
            })
            ->where('name', 'like', '%' . $search . '%')
            ->orderBy('name')
            ->limit(config('user.limit_user_query'))
            ->get();
    }
}
