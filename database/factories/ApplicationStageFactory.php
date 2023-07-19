<?php

namespace Database\Factories;

use App\Models\ApplicationStage;
use App\Models\ApplicationVersion;
use App\Models\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationStage>
 */
class ApplicationStageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_version_id' => ApplicationVersion::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => ApplicationStatus::Draft->value,
            'subsidy_stage_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
        ];
    }
}
