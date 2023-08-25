<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\IdentityType;


/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{

    protected $model = Application::class;
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
