<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Policies;

use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;

class SubidyPolicy
{
    public function viewBankAccountDuplicates(User $user, Subsidy $subsidy): bool
    {
        return $user->hasRole(Role::InternalAuditor) ||
            $user->hasRoleForSubsidy(Role::InternalAuditor, $subsidy);
    }
}
