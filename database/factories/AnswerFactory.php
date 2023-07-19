<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\ApplicationStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    public function definition():array
    {
        return [
            'application_stages_id' => ApplicationStage::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'field_id' => $this->faker->uuid,
            'encrypted_answer' => $this->faker->text,
        ];
    }
}
