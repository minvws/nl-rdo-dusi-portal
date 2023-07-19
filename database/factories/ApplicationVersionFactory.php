<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationVersion;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends Factory<ApplicationVersion>
 */
class ApplicationVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'version' => $this->faker->randomDigitNotZero(),
        ];
    }
}
