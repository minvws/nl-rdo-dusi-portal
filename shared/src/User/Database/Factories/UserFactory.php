<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password,
            'password_updated_at' => now()->addDays(180),
            'active_until' => null,
            'two_factor_secret' => null,
            'password_reset_token' => null,
            'organisation_id' => Organisation::factory(),
        ];
    }

    public function withPassword(string $password): Factory
    {
        return $this->state([
            'password' => Hash::make($password),
        ]);
    }

    public function withTwoFactorSecret(string $twoFactorSecret): Factory
    {
        return $this->state([
            'two_factor_secret' => encrypt($twoFactorSecret),
        ]);
    }

    /**
     * @param Role $role
     * @param Subsidy|null $subsidy
     * @return Factory
     */
    public function withRole(Role $role, ?Subsidy $subsidy = null): Factory
    {
        return $this->afterCreating(function (User $user) use ($subsidy, $role) {
            $user->attachRole($role, $subsidy?->id);
        });
    }
}
