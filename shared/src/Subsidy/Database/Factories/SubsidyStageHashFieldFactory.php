<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHashField;

/**
 * @extends Factory<SubsidyStageHashField>
 */
class SubsidyStageHashFieldFactory extends Factory
{
    protected $model = SubsidyStageHashField::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subsidy_stage_hash_id' => SubsidyStageHash::factory(),
            'field_id' => Field::factory(),
        ];
    }
}
