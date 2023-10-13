<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserRoleScope implements Scope
{
    public function __construct(protected array $allowedRoles)
    {
    }
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $model does not have to be used
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('roles', function (Builder $query) {
            $query->whereIn('name', $this->allowedRoles);
        });
    }
}
