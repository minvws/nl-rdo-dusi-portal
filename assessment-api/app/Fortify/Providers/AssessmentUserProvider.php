<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Database\Eloquent\Builder;
use MinVWS\DUSi\Shared\User\Models\User;
use RuntimeException;

class AssessmentUserProvider extends EloquentUserProvider
{
    protected array $allowedRoles = [];

    /**
     * Create a new database user provider.
     *
     * @param HasherContract $hasher
     * @param class-string $model
     * @param array $allowedRoles
     */
    public function __construct(HasherContract $hasher, string $model, array $allowedRoles = [])
    {
        parent::__construct($hasher, $model);

        $this->allowedRoles = $allowedRoles;

        $this->queryCallback = function (Builder $query) use ($allowedRoles) {
            /**
             * @psalm-suppress TooManyTemplateParams
             * @var Builder<User> $query
             */
            if (!($query->getModel() instanceof User)) {
                throw new RuntimeException('Could not add active and anyRole scope if not our User model');
            }

            $query
                ->active()
                ->anyRole($allowedRoles);
        };
    }
}
