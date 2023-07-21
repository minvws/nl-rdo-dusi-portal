<?php

namespace Database\Factories;

use App\Models\Subsidy;
use App\Models\SubsidyVersion;
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
            'id' => Uuid::uuid4(),
        ];
    }
}
