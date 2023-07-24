<?php

namespace Database\Factories;

use App\Models\Application;
use App\Shared\Models\Application\IdentityType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;


/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'subsidy_version_id' => $this->faker->uuid,
            'identity_type' => IdentityType::EncryptedCitizenServiceNumber->value,
            'identity_identifier' => $this->faker->randomNumber(9),
            'application_title' => $this->faker->words(3, true),
            'final_review_deadline' => $this->faker->dateTimeBetween('now', '+1 year'),
            'locked_from' => null,
        ];
    }
}
