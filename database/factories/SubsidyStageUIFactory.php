<?php

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageUI;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<SubsidyStageUI>
 */
class SubsidyStageUIFactory extends Factory
{
    protected $model = SubsidyStageUI::class;

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
