<?php

namespace Database\Factories;

use App\Models\VersionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormUI>
 */
class FormUIFactory extends Factory
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
