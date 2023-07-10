<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return (\DateTime|string)[]
     */
    public function definition(): array
    {
        return [
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            // 'form_id' => $this->faker->uuid,
            'form_id' => '29a444d8-0f36-4266-8881-489f7cfd2b1c' // BTV_V1_UUID = '29a444d8-0f36-4266-8881-489f7cfd2b1c';
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Application $application) {
            $application->judgement = $application->applicationReviews()->orderByDesc('created_at')->first()->judgement;
            $application->save();
        });
    }
}
