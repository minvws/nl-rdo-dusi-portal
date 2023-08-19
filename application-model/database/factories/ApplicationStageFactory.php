<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationStage>
 */
class ApplicationStageFactory extends Factory
{

    protected $model = ApplicationStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' =>$this->faker->uuid,
            'application_id' => Application::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'subsidy_stage_id' => $this->faker->uuid,
            'stage' => 1,
            'user_id' => $this->faker->uuid,
        ];
    }
}
