<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition\Factories;

use App\Shared\Models\Definition\Enums\FieldType;
use App\Shared\Models\Definition\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'id' => $this->faker->uuid,
            'title' => $this->faker->words(3, true),
            'type' => FieldType::Text,
            'params' => [],
            'is_required' => true,
            'code' => $this->faker->word,
            ];
    }
}
