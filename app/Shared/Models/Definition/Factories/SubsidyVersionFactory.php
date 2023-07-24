<?php

namespace App\Shared\Models\Definition\Factories;

use App\Shared\Models\Definition\Enums\VersionStatus;
use App\Shared\Models\Definition\SubsidyVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
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
            'id' => $this->faker->uuid,
            'version' => $this->faker->numberBetween(1, 10),
            'status' => VersionStatus::Draft,
        ];
    }
}
