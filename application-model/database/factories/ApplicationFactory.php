<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;


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
        $subsidyVersion = SubsidyVersion::factory()->create();

        return [
            'id' => $this->faker->uuid,
            'subsidy_version_id' => $subsidyVersion,
            'reference' => sprintf('%s-%s', $subsidyVersion->subsidy()->get()->first()->reference_prefix, $this->faker->unique()->regexify('[0-9]{8}')),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'identity_type' => IdentityType::EncryptedCitizenServiceNumber->value,
            'identity_identifier' => $this->faker->randomNumber(9),
            'application_title' => $this->faker->words(3, true),
            'final_review_deadline' => $this->faker->dateTimeBetween('now', '+1 year'),
            'locked_from' => null,
        ];
    }
}
