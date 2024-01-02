<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV\BTVSubsidyStagesSeeder;

class BTVAssessmentStageUISeeder extends Seeder
{
    public const BTV_STAGE2_UI_UUID = 'c2365ef6-5ff9-469f-ab4a-5533c33b299d';
    public const BTV_STAGE3_UI_UUID = '44914BC7-9E4F-4B79-9498-01ADBE5C4CFE';
    public const BTV_STAGE4_UI_UUID = 'E819DF05-03B7-4F37-B315-7F62339FD067';

    public function run(): void
    {
        $this->firstAssessment();
        $this->secondAssessment();
        $this->internalAssessment();
    }

    private function buildViewSchema(int $stage): array
    {
        $filePath = __DIR__ . sprintf('/resources/view_ui/stage%d.json', $stage);
        if (!file_exists($filePath)) {
            return [];
        }
        $json = file_get_contents($filePath);
        assert(is_string($json));
        return json_decode($json, true);
    }

    public function firstAssessment(): void
    {
        $view_ui = $this->buildViewSchema(2);

        $input_ui = [
            "type" => "FormGroupControl",
            "options" => [
                "section" => true,
                "group" => true
            ],
            "elements" => [
                [
                    "type" => "Group",
                    "label" => "Beoordeling",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/firstAssessmentChecklist",
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
                                    "scope" => "#/properties/firstAssessment",
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
                    "label" => "Toelichting",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/firstAssessmentRequestedComplementReason",
                                    "options" => [
                                        "format" => "radio"
                                    ],
                                    "rule" => [
                                        "effect" => "SHOW",
                                        "condition" =>  [
                                            "scope" => "#/properties/firstAssessment",
                                            "schema" =>  [
                                                "const" => "Aanvulling nodig"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/firstAssessmentRequestedComplementNote",
                                    "options" => [
                                        "format" => "textarea",
                                        "tip" => "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                                    ],
                                    "rule" => [
                                        "effect" => "SHOW",
                                        "condition" =>  [
                                            "scope" => "#/properties/firstAssessment",
                                            "schema" =>  [
                                                "const" => "Aanvulling nodig"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/firstAssessmentRejectedNote",
                                    "options" => [
                                        "format" => "textarea",
                                        "tip" => "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                                    ],
                                    "rule" => [
                                        "effect" => "SHOW",
                                        "condition" =>  [
                                            "scope" => "#/properties/firstAssessment",
                                            "schema" =>  [
                                                "const" => "Afgekeurd"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/firstAssessmentInternalNote",
                                    "options" => [
                                        "format" => "textarea"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
                                                   'id' => self::BTV_STAGE2_UI_UUID,
                                                   'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
                                                   'version' => 1,
                                                   'status' => 'published',
                                                   'input_ui' => json_encode($input_ui),
                                                   'view_ui' => json_encode($view_ui)
                                               ]);
    }

    public function secondAssessment(): void
    {
        $view_ui = $this->buildViewSchema(3);

        $input_ui = [
            "type" => "FormGroupControl",
            "options" => [
                "section" => true,
                "group" => true
            ],
            "elements" => [
                [
                    "type" => "Group",
                    "label" => "Status",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/firstAssessorMotivatedValid",
                                    "options" => [
                                        "format" => "checkbox"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/secondAssessment",
                                    "options" => [
                                        "format" => "radio"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/secondAssessmentInternalNote",
                                    "options" => [
                                        "format" => "textarea"
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
                                                   'id' => self::BTV_STAGE3_UI_UUID,
                                                   'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
                                                   'version' => 1,
                                                   'status' => 'published',
                                                   'input_ui' => json_encode($input_ui),
                                                   'view_ui' => json_encode($view_ui)
                                               ]);
    }

    public function internalAssessment(): void
    {
        $view_ui = $this->buildViewSchema(4);

        $input_ui = [
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
                                    "scope" => "#/properties/internalAssessmentChecklist",
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
                    "label" => "Status",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/internalAssessment",
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
                    "label" => "Toelichting",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/internalAssessmentInternalNote",
                                    "options" => [
                                        "format" => "textarea"
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],

            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
                                                   'id' => self::BTV_STAGE4_UI_UUID,
                                                   'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
                                                   'version' => 1,
                                                   'status' => 'published',
                                                   'input_ui' => json_encode($input_ui),
                                                   'view_ui' => json_encode($view_ui)
                                               ]);
    }
}
