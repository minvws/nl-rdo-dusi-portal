<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition\Factories;

use App\Shared\Models\Definition\SubsidyStage;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'id' => $this->faker->uuid,
            'subsidy_version_id' => $this->faker->uuid,
            'title' => $this->faker->sentence,
            'subject_role' => 'applicant',
            'stage' => 1,
            'final_review_deadline' => $this->faker->dateTimeBetween('now', '+1 year'),
            'final_review_time_in_s_after_submission' => 60 * 60 * 24 * 7,
        ];
    }
}
