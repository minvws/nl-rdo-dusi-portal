<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\User\Models\Organisation;

/**
 * @phpstan-extends Factory<Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Organisation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
