<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Application> */
class AnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'field_id' => $this->faker->uuid,
            'encrypted_answer' => $this->faker->text,
        ];
    }
}
