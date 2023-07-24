<?php

namespace Database\Factories;

use App\Models\ApplicationStage;
use App\Models\Application;
use App\Models\Enums\ApplicationStageStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

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
            'id' =>$this->faker->uuid,
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => ApplicationStageStatus::Draft->value,
            'subsidy_stage_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
        ];
    }
}
