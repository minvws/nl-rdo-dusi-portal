<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormUIsTableSeeder extends Seeder
{
    public const BTV_V1_UUID = '72475863-7987-4375-94d7-21e04ff6552b';

    public function run(): void
    {
        $page1 = [
            'type' => 'HorizontalLayout',
            'elements' => [
                [
                    'type' => 'FormHtml',
                    'options' => [
                        'html' => file_get_contents(__DIR__ . '/btv-intro.html'),
                    ]
                ]
            ]
        ];

        $page2 = [
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
                                "format" => "select",
                                "placeholder" => "Selecteer een optie..."
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
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/houseNumberSuffix"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/postalCode"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/city"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/country"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/phoneNumber"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/bankAccountNumber"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/bankAccountHolder"
                        ]
                    ]
                ]
            ]
        ];

        $page3 = [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Controleren, uploaden en verzenden",
            "elements" => [
                [
                    "type" => "VerticalLayout",
                    "elements" => [
                        [
                            "type" => "FormHtml",
                            "options" => ['html' => '<h3>Controleren</h5><p>Controleer hieronder uw gegevens.</p>']
                        ],
                        [
                            "type" => "FormResultsTable",
                            "options" => [
                                "fields" => [
                                    'Indiener' => '{firstName} {infix} {lastName}'
                                ]
                            ]
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/bankStatement"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/extractPersonalRecordsDatabase"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/proofOfMedicalTreatment"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/proofOfTypeOfMedicalTreatment"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/permissionToProcessPersonalData"
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
                        'required' => []
                    ]
                ],
                [
                    'type' => 'CustomPageControl',
                    'label' => 'Contactgegevens aanvrager',
                    'elements' => [$page2],
                    'options' => [
                        'required' => [
                            'formOfAddress',
                            'firstName',
                            'lastName'
                        ]
                    ]
                ],
                [
                    'type' => 'CustomPageControl',
                    'label' => 'Controleren, uploaden en verzenden',
                    'elements' => [$page3]
                ]
            ]
        ];

        DB::table('form_uis')->insert([
            'id' => self::BTV_V1_UUID,
            'form_id' => FormsTableSeeder::BTV_V1_UUID,
            'version' => 1,
            'status' => 'published',
            'ui' => json_encode($ui)
        ]);
    }
}
