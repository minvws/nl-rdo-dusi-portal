<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition\Factories;

use App\Shared\Models\Definition\Subsidy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Shared\Models\Definition\Subsidy>
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
            'title' => $this->faker->words(asText: true),
            'description' => $this->faker->paragraph,
            'valid_from' => $this->faker->dateTimeBetween('3 years ago', '3 months ago'),
            'valid_to' => $this->faker->dateTimeBetween('next month', 'now + 3 years'),
        ];
    }
}
