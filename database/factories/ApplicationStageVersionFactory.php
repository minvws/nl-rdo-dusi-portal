<?php

namespace Database\Factories;

use App\Models\ApplicationStage;
use App\Models\ApplicationStageVersion;
use App\Models\Enums\ApplicationStageVersionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;


/**
 * @extends Factory<ApplicationStageVersion>
 */
class ApplicationStageVersionFactory extends Factory
{
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
