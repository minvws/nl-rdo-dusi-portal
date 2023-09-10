<?php

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<Subsidy>
 */
class SubsidyFactory extends Factory
{
    protected $model = Subsidy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'reference_prefix' => $this->faker->unique()->regexify('[A-Z]{4}[0-9]{2}'),
            'title' => $this->faker->words(3, true),
            'code' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'description' => $this->faker->paragraph,
            'valid_from' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'valid_to' => $this->faker->dateTimeBetween('-1 year', '+1 year')
        ];
    }
}
