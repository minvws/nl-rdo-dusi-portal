<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\Role;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => RoleEnum::Assessor,
            'view_all_stages' => false,
        ];
    }

    public function withRoleFromEnum(RoleEnum $role): Factory
    {
        return $this->state([
            'name' => $role->value,
        ]);
    }

    public function withAllStageAccess(bool $stageAccess): Factory
    {
        return $this->state([
            'view_all_stages' => $stageAccess,
        ]);
    }
}
