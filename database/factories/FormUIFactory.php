<?php

namespace Database\Factories;

use App\Models\FormUI;
use App\Models\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<FormUI>
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
