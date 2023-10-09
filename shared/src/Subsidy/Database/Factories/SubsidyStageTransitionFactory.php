<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

/**
 * @extends Factory<SubsidyStageTransition>
 */
class SubsidyStageTransitionFactory extends Factory
{
    protected $model = SubsidyStageTransition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'current_subsidy_stage_id' => SubsidyStage::factory(),
            'target_subsidy_stage_id' => null,
            'target_application_status' => null,
            'condition' => null,
            'clone_data' => false,
            'assign_to_previous_assessor' => false
        ];
    }
}
