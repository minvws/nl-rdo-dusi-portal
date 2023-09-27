<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

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
            'application_stage_id' => fn () => ApplicationStage::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'field_id' => Field::factory(),
            'encrypted_answer' => json_encode($this->faker->text),
        ];
    }
}
