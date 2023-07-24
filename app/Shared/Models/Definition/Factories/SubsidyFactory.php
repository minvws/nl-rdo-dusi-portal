<?php

namespace App\Shared\Models\Definition\Factories;

use App\Shared\Models\Definition\Subsidy;
use Illuminate\Database\Eloquent\Factories\Factory;
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
            'id' => $this->faker->uuid,
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'valid_from' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'valid_to' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
        ];
    }
}
