<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Field;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'field_id' => Field::all()->random()->id,
            'encrypted_answer' => $this->faker->text,
            'encryption_key_id' => $this->faker->uuid,
        ];
    }
}
