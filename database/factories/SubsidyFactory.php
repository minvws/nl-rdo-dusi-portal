<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subsidy>
 */
class SubsidyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'title' => $this->faker->words(asText: true),
            'description' => $this->faker->paragraph,
            'valid_from' => $this->faker->dateTimeBetween('3 years ago', '3 months ago'),
            'valid_to' => $this->faker->dateTimeBetween('next month', 'now + 3 years'),
        ];
    }
}
