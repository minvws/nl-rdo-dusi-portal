<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
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
            'reference' => sprintf(
                '%s-%s',
                $subsidyVersion->subsidy()->get()->first()->reference_prefix,
                $this->faker->unique()->regexify('[0-9]{8}')
            ),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'identity_id' => Identity::factory(),
            'application_title' => $this->faker->words(3, true),
            'final_review_deadline' => $this->faker->dateTimeBetween('now', '+1 year'),
            'locked_from' => null,
            'status' => ApplicationStatus::Draft
        ];
    }

    public function forIdentity(Identity $identity): self
    {
        return $this->state(fn () => [
            'identity_id' => $identity,
        ]);
    }
}
