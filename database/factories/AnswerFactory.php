<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * @return (Factory|\DateTime|string)[]
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'question_id' => $this->faker->uuid,
            'encrypted_answer' => $this->faker->text,
            'encryption_key_id' => $this->faker->uuid,
        ];
    }
}
