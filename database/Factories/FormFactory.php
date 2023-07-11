<?php

namespace App\Shared\Models\Definition\Factories;

use App\Models\Form;
use App\Models\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'version' => 1,
            'status' => VersionStatus::Draft
        ];
    }
}
