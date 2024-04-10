<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentStageUISeeder extends Seeder
{
    public const ASSESSMENT_STAGE2_UI_UUID = '26a15797-01ec-4d19-b10d-618c0507c36f';
    public const ASSESSMENT_STAGE3_UI_UUID = '31b8c514-10eb-4e2b-a882-04b8dc2d3711';
    public const ASSESSMENT_STAGE4_UI_UUID = '47df4a63-6bfe-49da-a684-2e948d9c4c50';
    public const ASSESSMENT_STAGE5_UI_UUID = '30c57b87-dd07-423e-908e-39865c10ef21';

    public function run(): void
    {
        $this->firstAssessment();
        $this->secondAssessment();
        $this->internalAssessment();
        $this->implementationCoordinatorAssessment();
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
                    "label" => "Persoonsgegevens",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/personalDataChecklist",
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
                    "label" => "Vaststellen WIA",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/wiaChecklist",
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
                                    "scope" => "#/properties/WIADecisionIndicates",
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
                                    "scope" => "#/properties/IVA_Or_WIA_Checklist",
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
                                    "scope" => "#/properties/WIA_RejectedOnHighSalaryChecklist",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
                [
                    "type" => "Group",
                    "label" => "Zorgaanbieder en functie",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/employerChecklist",
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
                                    "scope" => "#/properties/healthcareProviderStatementIsComplete",
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
                                    "scope" => "#/properties/employerName",
                                    "options" => [
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/healthcareProviderName",
                                    "options" => [
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/chamberOfCommerceNumberHealthcareProvider",
                                    "options" => [
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/healthcareProviderChecklist",
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
                                    "scope" => "#/properties/healthcareProviderSBICode",
                                    "options" => [
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/healthcareProviderAGBCode",
                                    "options" => [
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
                [
                    "type" => "Group",
                    "label" => "Justitiële inrichting",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/judicialInstitutionIsEligible",
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
                                    "scope" => "#/properties/applicantFoundInBigRegister",
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
                    "label" => "Vaststellen post-COVID",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/postCovidChecklist",
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
                                    "scope" => "#/properties/doctorFoundInBigRegister",
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
                                    "scope" => "#/properties/doctorsCertificateIsComplete",
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
            'id' => self::ASSESSMENT_STAGE2_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
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
            'id' => self::ASSESSMENT_STAGE3_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::STAGE_3_UUID,
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
            'id' => self::ASSESSMENT_STAGE4_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::STAGE_4_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function implementationCoordinatorAssessment(): void
    {
        $view_ui = $this->buildViewSchema(5);

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
                                    "scope" => "#/properties/coordinatorImplementationReasonForRejection",
                                    "options" => [
                                        "format" => "textarea",
                                        "tip" => "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                                    ],
                                    "rule" => [
                                        "effect" => "SHOW",
                                        "condition" =>  [
                                            "scope" => "#/properties/implementationCoordinatorAssessment",
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
                                    "scope" => "#/properties/coordinatorImplementationInternalNote",
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
            'id' => self::ASSESSMENT_STAGE5_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::STAGE_5_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
