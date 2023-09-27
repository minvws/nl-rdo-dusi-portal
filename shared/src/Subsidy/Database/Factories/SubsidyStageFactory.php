<?php

declare(strict_types=1);

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
            'id' => $this->faker->uuid(),
            'subsidy_version_id' => SubsidyVersion::factory(),
            'title' => $this->faker->sentence,
            'subject_role' => 'applicant',
            'stage' => 1
        ];
    }
}
