<?php

namespace factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
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
            'code' => $this->faker->word,
            'title' => $this->faker->words(3, true),
            'type' => 'text',
            'is_required' => true
        ];
    }
}
