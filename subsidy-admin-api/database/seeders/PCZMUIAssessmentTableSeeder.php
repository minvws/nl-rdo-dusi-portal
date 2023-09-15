<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PCZMUIAssessmentTableSeeder extends Seeder
{
    public const PCZM_STAGE2_V1_UUID = '71f71916-c0ed-45bc-8186-1b4f5dfb69e8';

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

        $view_ui = [
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

        $ui = [
            "type" => "FormGroupControl",
            "options" => [
                "section" => true,
                "group" => true
            ],
            "elements" => [
                [
                    "type" => "Group",
                    "label" => "Checklist",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/checklist",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "Group",
                    "label" => "Uitkering",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/amount",
                                    "options" => [
                                        "format" => "radio"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "Group",
                    "label" => "Status",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/review",
                                    "options" => [
                                        "format" => "radio"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::PCZM_STAGE2_V1_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
