<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentStageUISeeder extends Seeder
{
    public const SUBSIDY_STAGE2_UI_UUID = '4aa24ca1-0fa8-45d3-a632-15fd788fbc6e';
    public const SUBSIDY_STAGE3_UI_UUID = '8f7b2a5f-050e-4dd2-9d05-4e1d20f3929a';
    public const SUBSIDY_STAGE4_UI_UUID = '6a669ec1-e949-40d8-bbc4-946665553fb1';

    public function run(): void
    {
        $this->firstAssessment();
        $this->internalAssessment();
        $this->implementationAssessment();
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
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/subsidyAwardedBefore",
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
                                    "scope" => "#/properties/firstAssessmentApprovedNote",
                                    "options" => [
                                        "format" => "textarea",
                                        "tip" => "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                                    ],
                                    "rule" => [
                                        "effect" => "SHOW",
                                        "condition" =>  [
                                            "scope" => "#/properties/firstAssessment",
                                            "schema" =>  [
                                                "const" => "Goedgekeurd"
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
            'id' => self::SUBSIDY_STAGE2_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function internalAssessment(): void
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
                                    "scope" => "#/properties/internalAssessment",
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
                                    "scope" => "#/properties/internalAssessmentInternalNote",
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
            'id' => self::SUBSIDY_STAGE3_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function implementationAssessment(): void
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
                    "label" => "Status",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/implementationCoordinatorAssessment",
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
                                    "scope" => "#/properties/implementationCoordinatorAssessmentInternalNote",
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
            'id' => self::SUBSIDY_STAGE4_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
