<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return (Factory|\DateTime|mixed|string)[]
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'encrypted_comment' => $this->faker->text,
            'encryption_key_id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
            'judgement' => $this->faker->randomElement(['approved', 'rejected', 'pending']),
        ];
    }
}
