<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use DateTime;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;

/**
 * @extends Factory<ApplicationStage>
 */
class ApplicationStageFactory extends Factory
{
    protected $model = ApplicationStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'application_id' => fn () => Application::factory(),
            'subsidy_stage_id' => fn () => SubsidyStage::factory(),
            'sequence_number' => 1,
            'is_current' => true,
            'is_submitted' => false,
            'submitted_at' => null,
            'encrypted_key' => new HsmEncryptedData('', ''),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now')
        ];
    }

    public function submitted(?DateTime $submittedAt = null): static
    {
        return $this->state(function (array $attributes) use ($submittedAt) {
            $closedAt = $submittedAt ?? $this->faker->dateTimeBetween($attributes['created_at']);
            return [
                'closed_at' => $closedAt,
                'submitted_at' => $closedAt,
                'is_current' => false,
                'is_submitted' => true,
            ];
        });
    }
}
