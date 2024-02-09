<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

/**
 * @extends Factory<ApplicationStageTransition>
 */
class ApplicationStageTransitionFactory extends Factory
{
    protected $model = ApplicationStageTransition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'application_id' => fn () => Application::factory(),
            'subsidy_stage_transition_id' => fn () => SubsidyStageTransition::factory(),
            'previous_application_stage_id' => fn () => ApplicationStage::factory(),
            'new_application_stage_id' => fn () => ApplicationStage::factory(),
            'previous_application_status' => ApplicationStatus::Draft,
            'new_application_status' => ApplicationStatus::Pending,
            'created_at' => $this->faker->dateTimeBetween('-1 month')
        ];
    }
}
