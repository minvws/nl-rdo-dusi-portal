<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;

/**
 * @extends Factory<ApplicationStageVersion>
 */
class ApplicationStageVersionFactory extends Factory
{
    protected $model = ApplicationStageVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'application_stage_id' => ApplicationStage::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'status' => ApplicationStageVersionStatus::Draft->value,
            'version' => $this->faker->randomDigitNotZero(),
        ];
    }
}
