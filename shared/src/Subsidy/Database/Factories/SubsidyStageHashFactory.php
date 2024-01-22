<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;

/**
 * @extends Factory<SubsidyStageHash>
 */
class SubsidyStageHashFactory extends Factory
{
    protected $model = SubsidyStageHash::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
        ];
    }
}
