<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\ApplicationStageVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'application_stage_version_id' => ApplicationStageVersion::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'field_id' => $this->faker->uuid,
            'encrypted_answer' => $this->faker->text,
        ];
    }
}
