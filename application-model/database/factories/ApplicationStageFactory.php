<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

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
        $_subsidy_version = null;
        $subsidy_version = function () use (&$_subsidy_version) {
            if (!isset($_subsidy_version)) {
                $_subsidy_version = SubsidyVersion::factory()->for(Subsidy::factory())->create();
            }
            return $_subsidy_version;
        };
        return [
            'id' => $this->faker->uuid,
            'application_id' => fn() => Application::factory()->for($subsidy_version()),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'subsidy_stage_id' => fn() => SubsidyStage::factory()->for($subsidy_version()),
            'stage' => 1,
            'user_id' => $this->faker->uuid,
        ];
    }
}
