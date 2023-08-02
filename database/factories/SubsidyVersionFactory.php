<?php

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<SubsidyVersion>
 */
class SubsidyVersionFactory extends Factory
{
    protected $model = SubsidyVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'version' => $this->faker->randomDigitNotZero(),
            'status' => VersionStatus::Draft->value,
        ];
    }
}
