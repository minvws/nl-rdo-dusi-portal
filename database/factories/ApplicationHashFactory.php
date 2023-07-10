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
    /**
     * @return (Factory|string)[]
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'form_hash_id' => $this->faker->uuid,
            'hash' => $this->faker->text,
        ];
    }
}
