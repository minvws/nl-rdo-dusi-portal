<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PCZMUIAssessmentTableSeeder extends Seeder
{
    public const PCZM_V1_STAGE2_UI_UUID = '71F71916-C0ED-45BC-8186-1B4F5DFB69E8';
    public const PCZM_V1_STAGE3_UI_UUID = '44914BC7-9E4F-4B79-9498-01ADBE5C4CFE';
    public const PCZM_V1_STAGE4_UI_UUID = 'E819DF05-03B7-4F37-B315-7F62339FD067';
    public const PCZM_V1_STAGE5_UI_UUID = 'C51302F6-E131-45FF-8D4B-F4FF4A39B52F';

    public function run(): void
    {
        $this->firstAssessment();
        $this->secondAssessment();
        $this->internalAssessment();
        $this->implementationCoordinatorAssessment();
    }

    public function firstAssessment(): void
    {
        $applicationSection = $this->getAssessmentSection();

        $firstAssessmentSection = $this->getFirstAssessmentSection();

        $view_ui = [
            'type' => 'FormGroupControl',
            'elements' => [
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Aanvraag',
                    'elements' => [$applicationSection],
                    'options' => [
                    ]
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Eerste beoordeling',
                    'elements' => [$firstAssessmentSection],
                    'options' => [
                        'required' => ['firstAssessment']
                    ]
                ]
            ]
        ];

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
                                        "format" => "checkbox"
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
                                    "scope" => "#/properties/firstSickDayWithinExpiryDate",
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
                    "label" => "Zorgaanbieder en functie",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/employerChecklist",
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
                                    "scope" => "#/properties/chamberOfCommerceNumberEmployer",
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
                                    "scope" => "#/properties/chamberOfCommerceNumberHealtcareProvider",
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
                                    "scope" => "#/properties/docterFoundInBigRegister",
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
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::PCZM_V1_STAGE2_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function secondAssessment(): void
    {
        $applicationSection = $this->getAssessmentSection();

        $firstAssessmentSection = $this->getFirstAssessmentSection();

        $secondAssessmentSection = $this->getSecondAssessmentSection();

        $view_ui = [
            'type' => 'FormGroupControl',
            'elements' => [
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Aanvraag',
                    'elements' => [$applicationSection],
                    'options' => [
                    ]
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Eerste aanvraag',
                    'elements' => [$firstAssessmentSection],
                    'options' => [
                    ]
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Tweede aanvraag',
                    'elements' => [$secondAssessmentSection],
                    'options' => [
                        'required' => ['secondAssessment']
                    ]
                ]
            ]
        ];

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
                                    "scope" => "#/properties/secondAssessment",
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
            'id' => self::PCZM_V1_STAGE3_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function internalAssessment(): void
    {
        $applicationSection = $this->getAssessmentSection();

        $firstAssessmentSection = $this->getFirstAssessmentSection();

        $secondAssessmentSection = $this->getSecondAssessmentSection();

        $internalAssessmentSection = $this->getInternalAssessmentSection();

        $view_ui = [
            'type' => 'FormGroupControl',
            'elements' => [
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Aanvraag',
                    'elements' => [$applicationSection],
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Eerste beoordeling',
                    'elements' => [$firstAssessmentSection],
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Tweede beoordeling',
                    'elements' => [$secondAssessmentSection],
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Interne controle',
                    'elements' => [$internalAssessmentSection],
                ]
            ]
        ];

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
                                    "scope" => "#/properties/internalAssessment",
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
            'id' => self::PCZM_V1_STAGE4_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function implementationCoordinatorAssessment(): void
    {
        $applicationSection = $this->getAssessmentSection();

        $firstAssessmentSection = $this->getFirstAssessmentSection();

        $secondAssessmentSection = $this->getSecondAssessmentSection();

        $internalAssessmentSection = $this->getInternalAssessmentSection();

        $implementationCoordinatorAssessmentSection = $this->getImplementationCoordinatorAssessmentSection();

        $view_ui = [
            'type' => 'FormGroupControl',
            'elements' => [
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Aanvraag',
                    'elements' => [$applicationSection],
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Eerste beoordeling',
                    'elements' => [$firstAssessmentSection],
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Tweede beoordeling',
                    'elements' => [$secondAssessmentSection],
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Interne controle',
                    'elements' => [$internalAssessmentSection],
                ],
                [
                    'type' => 'FormGroupControl',
                    'label' => 'Uitvoeringscoördinator controle',
                    'elements' => [$implementationCoordinatorAssessmentSection],
                ]
            ]
        ];

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
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::PCZM_V1_STAGE5_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function getAssessmentSection(): array
    {
        $applicationSection = [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Aanvraag",
            "elements" => [
                [
                    "type" => "VerticalLayout",
                    "elements" => [
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

        return $applicationSection;
    }

    public function getFirstAssessmentSection(): array
    {
        return [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Eerste beoordeling",
            "elements" => [
                [
                    "type" => "VerticalLayout",
                    "elements" => [
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/checklist",
                            "options" => [
                                "format" => "checkbox"
                            ]
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/amount",
                            "options" => [
                                "format" => "radio"
                            ]
                        ],
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
        ];
    }

    public function getSecondAssessmentSection(): array
    {
        return [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Tweede beoordeling",
            "elements" => [
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
                ]
            ]
        ];
    }

    public function getInternalAssessmentSection(): array
    {
        return [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Interne controle",
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
        ];
    }

    public function getImplementationCoordinatorAssessmentSection(): array
    {
        return [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Uitvoeringscoördinator controle",
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
        ];
    }
}
