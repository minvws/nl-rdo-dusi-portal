<?php

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use Ramsey\Uuid\Uuid;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

/**
 * @extends Factory<SubsidyStage>
 */
class SubsidyStageFactory extends Factory
{
    protected $model = SubsidyStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'subsidy_version_id' => SubsidyVersion::factory(),
            'title' => $this->faker->sentence,
            'subject_role' => 'applicant',
            'stage' => 1,
            'final_review_deadline' => $this->faker->dateTimeBetween('now', '+1 year'),
            'final_review_time_in_s_after_submission' => 60 * 60 * 24 * 7,
        ];
    }
}
