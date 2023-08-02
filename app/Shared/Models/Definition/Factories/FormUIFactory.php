<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition\Factories;

use App\Shared\Models\Definition\FormUI;
use App\Shared\Models\Definition\VersionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Shared\Models\Definition\FormUI>
 */
class FormUIFactory extends Factory
{
    protected $model = FormUI::class;

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
            'status' => VersionStatus::Draft,
            'ui' => [
                "type" => "CustomGroupControl",
                "options" => [
                    "section" => true
                ],
                "label" => "Section",
                "elements" => [
                    [
                        "type" => "VerticalLayout",
                        "elements" => []
                    ]
                ]
            ]
        ];
    }
}
