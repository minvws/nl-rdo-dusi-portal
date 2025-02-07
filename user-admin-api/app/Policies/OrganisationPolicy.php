<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Policies;

use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\User;

class OrganisationPolicy
{
    /**
     * Perform pre-authorization checks.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isUserAdministrator()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewAny(User $user): bool
    {
        //
        return false;
    }

    /**
     * Determine whether the user can view the model.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function view(User $user, Organisation $organisation): bool
    {
        //
        return false;
    }

    /**
     * Determine whether the user can create models.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(User $user): bool
    {
        //
        return false;
    }

    /**
     * Determine whether the user can update the model.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function update(User $user, Organisation $organisation): bool
    {
        //
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete(User $user, Organisation $organisation): bool
    {
        //
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function restore(User $user, Organisation $organisation): bool
    {
        //
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function forceDelete(User $user, Organisation $organisation): bool
    {
        //
        return false;
    }
}
