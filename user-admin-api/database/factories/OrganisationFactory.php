<?php

namespace MinVWS\DUSi\User\Admin\API\Database\Factories;

use MinVWS\DUSi\User\Admin\API\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @phpstan-extends Factory<Organisation>
 */
class OrganisationFactory extends Factory
{
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
