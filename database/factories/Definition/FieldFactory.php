<?php

namespace Database\Factories\Definition;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Definition\Form>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'label' => $this->faker->words(3, true),
            'type' => 'text',
            'is_required' => true,
            'sort' => 1
        ];
    }
}
