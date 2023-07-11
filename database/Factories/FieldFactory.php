<?php

namespace App\Shared\Models\Definition\Factories;

use App\Models\Field;
use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<Form>
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
