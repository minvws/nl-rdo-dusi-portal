<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationHash>
 */
class ApplicationHashFactory extends Factory
{
    protected $model = ApplicationHash::class;

    public function definition():array
    {
        return [
            'application_id' => Application::factory(),
            'subsidy_stage_hash_id' => $this->faker->uuid,
            'hash' => $this->faker->text,
        ];
    }
}
