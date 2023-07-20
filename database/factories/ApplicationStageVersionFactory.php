<?php

namespace Database\Factories;

use App\Models\ApplicationStage;
use App\Models\ApplicationStageVersion;
use Illuminate\Database\Eloquent\Factories\Factory;


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
            'application_stages_id' => ApplicationStage::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'version' => $this->faker->randomDigitNotZero(),
        ];
    }
}
