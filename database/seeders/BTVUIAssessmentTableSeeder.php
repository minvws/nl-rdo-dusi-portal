<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BTVUIAssessmentTableSeeder extends Seeder
{
    public const BTV_STAGE2_V1_UUID = 'c2365ef6-5ff9-469f-ab4a-5533c33b299d';

    public function run(): void
    {
        $page1 = [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Contactgegevens aanvrager",
            "elements" => [
                [
                    "type" => "VerticalLayout",
                    "elements" => [
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/formOfAddress",
                            "options" => [
                                "format" => "radio"
                            ]
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/firstName"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/infix"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/lastName"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/street"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/houseNumber"
                        ]
                    ]
                ]
            ]
        ];

        $ui = [
            'type' => 'CustomPageNavigationControl',
            'elements' => [
                [
                    'type' => 'CustomPageControl',
                    'label' => 'Start',
                    'elements' => [$page1],
                    'options' => [
                        'required' => ['firstname']
                    ]
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE2_V1_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::BTV_STAGE_2_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($ui),
            'view_ui' => json_encode($ui)
        ]);
    }
}
