<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

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
