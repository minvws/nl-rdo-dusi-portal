<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationHash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationHash>
 */
class ApplicationHashFactory extends Factory
{
    public function definition():array
    {
        return [
            'application_id' => Application::factory(),
            'subsidy_stage_hash_id' => $this->faker->uuid,
            'hash' => $this->faker->text,
        ];
    }
}
