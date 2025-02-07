<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Services;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Models\User;

class UserService
{
    public function getSubsidiesForUser(User $user): Collection
    {
        /**
         * @psalm-suppress InvalidTemplateParam
         */
        return $user->roles
            ->map(function (Role $role) {
                return $role->pivot->subsidy()->get();
            })
            ->flatten()
            ->unique('id');
    }

    /**
     * @description When a user has no subsidy attached to one of his roles, this means he as access to all subsidies.
     */
    public function hasAccessToAllSubsidies(User $user): bool
    {
        return $user->roles->contains(function (Role $role) {
            return $role->pivot->subsidy()->count() === 0;
        });
    }
}
