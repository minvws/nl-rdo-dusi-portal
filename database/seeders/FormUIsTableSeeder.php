<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormUIsTableSeeder extends Seeder
{
    public const BTV_V1_UUID = '72475863-7987-4375-94d7-21e04ff6552b';

    public function run(): void
    {
        $ui = [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "contactInformation.section",
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

        DB::table('form_uis')->insert([
            'id' => self::BTV_V1_UUID,
            'form_id' => FormsTableSeeder::BTV_V1_UUID,
            'version' => 1,
            'status' => 'published',
            'ui' => json_encode($ui)
        ]);
    }
}
