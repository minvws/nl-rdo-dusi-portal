<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition\Factories;

use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\VersionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Shared\Models\Definition\Form>
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
