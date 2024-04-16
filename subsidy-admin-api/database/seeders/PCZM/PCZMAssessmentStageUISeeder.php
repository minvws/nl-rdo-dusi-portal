<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PCZMAssessmentStageUISeeder extends Seeder
{
    public const PCZM_V1_STAGE2_UI_UUID = '71F71916-C0ED-45BC-8186-1B4F5DFB69E8';
    public const PCZM_V1_STAGE3_UI_UUID = '44914BC7-9E4F-4B79-9498-01ADBE5C4CFE';
    public const PCZM_V1_STAGE4_UI_UUID = 'E819DF05-03B7-4F37-B315-7F62339FD067';
    public const PCZM_V1_STAGE5_UI_UUID = 'C51302F6-E131-45FF-8D4B-F4FF4A39B52F';
    public const PCZM_V1_STAGE6_UI_UUID = '0521b7fd-fd90-4fbe-af16-c2df2d21a0b0';

    public function run(): void
    {
        $this->firstAssessment();
        $this->secondAssessment();
        $this->internalAssessment();
        $this->implementationCoordinatorAssessment();
        $this->increasedGrantStage();
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
            'id' => self::PCZM_V1_STAGE2_UI_UUID,
            'subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
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
            'id' => self::PCZM_V1_STAGE3_UI_UUID,
            'subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
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
            'id' => self::PCZM_V1_STAGE4_UI_UUID,
            'subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_4_UUID,
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
            'id' => self::PCZM_V1_STAGE5_UI_UUID,
            'subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function increasedGrantStage(): void
    {
        $view_ui = $this->buildViewSchema(6);

        $input_ui = [
            "type" => "FormGroupControl",
            "options" => [
                "section" => true,
                "group" => true
            ],
            "elements" => [
                [
                    "type" => "VerticalLayout",
                    "elements" => [
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/increasedGrantInternalNote",
                            "options" => [
                                "format" => "textarea"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::PCZM_V1_STAGE6_UI_UUID,
            'subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_6_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
