<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
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
        return [
            'id' => $this->faker->uuid,
            'subsidy_version_id' => SubsidyVersion::factory(),
            'reference' => function (array $attributes) {
                $subsidyVersion = SubsidyVersion::findOrFail($attributes['subsidy_version_id']);
                assert($subsidyVersion instanceof SubsidyVersion);

                $subsidy = $subsidyVersion->subsidy()->first();
                assert($subsidy instanceof Subsidy);

                return sprintf(
                    '%s-%s',
                    $subsidy->reference_prefix,
                    $this->faker->unique()->regexify('[0-9]{8}')
                );
            },
            'created_at' => $this->faker->dateTimeBetween('-3 month'),
            'identity_id' => Identity::factory(),
            'application_title' => $this->faker->words(3, true),
            'final_review_deadline' => null,
            'locked_from' => null,
            'status' => ApplicationStatus::Draft,
        ];
    }

    public function forIdentity(Identity $identity): self
    {
        return $this->state(fn () => [
            'identity_id' => $identity,
        ]);
    }

    public function withApplicantStage(SubsidyStage $subsidyStage): self
    {
        return $this->afterCreating(function (Application $application) use ($subsidyStage) {
            ApplicationStage::factory()
                ->for($application)
                ->for($subsidyStage)
                ->create();
        });
    }
}
