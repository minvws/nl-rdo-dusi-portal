<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<Field>
 */
class FieldFactory extends Factory
{
    protected $model = Field::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'subsidy_stage_id' => SubsidyStage::factory(),
            'code' => $this->faker->word,
            'title' => $this->faker->words(3, true),
            'type' => 'text',
            'is_required' => true,
            'retention_period_on_approval' => DataRetentionPeriod::Short->value,
        ];
    }
}
