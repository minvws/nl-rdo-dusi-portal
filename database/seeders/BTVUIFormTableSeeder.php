<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BTVUIFormTableSeeder extends Seeder
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
                            "scope" => "#/properties/email"
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/communicationPreference",
                            "options" => [
                                "format" => "radio"
                            ]
                        ],
                        [
                            "type" => "Group",
                            "label" => "Bankrekening",
                            "elements" => [
                                [
                                    "type" => "VerticalLayout",
                                    "elements" => [
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/bankAccountHolder"
                                        ],
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/bankAccountNumber"
                                        ]
                                    ]
                                ]
                            ]
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
                                    'Indiener' => '{firstName} {infix} {lastName}',
                                    'Adres' => '{street} {houseNumber}{houseNumberSuffix} {postalCode} {city}',
                                    'Telefoon' => '{phoneNumber}',
                                    'E-mailadress' => '{email}'
                                ]
                            ]
                        ],
                        [
                            "type" => "FormHtml",
                            "options" => ['html' => '<h3>Uploaden documenten</h3><p>Hieronder kunt u het uittreksel van het bevolkingsregister, de verklaringen van de arts (volgens de formats) en een scan/foto van een bankafschrift of een bankpas uploaden.</p>']
                        ],
                        [
                            "type" => "FormHtml",
                            "options" => ['html' => '<h3>Bankafschrift of bankpas</h3><p>Op de kopie van een recent bankafschrift moeten het rekeningnummer en uw naam zichtbaar zijn. Adres en datum mogen ook, maar zijn niet verplicht. Maak de andere gegevens onleesbaar. U mag ook een afdruk van internet bankieren gebruiken of een kopie van uw bankpas. Zie ook dit <a title="Voorbeeld bankafschrift op dus-i.nl " href="https://www.dus-i.nl/documenten/publicaties/2018/07/30/voorbeeld-bankafschrift" target="_blank" rel="noopener" class="external">voorbeeld</a>.</p>']
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/bankStatement"
                        ],
                        [
                            "type" => "FormResultsTable",
                            "options" => [
                                "fields" => [
                                    'Tenaamstelling rekeningnummer' => '{bankAccountHolder}',
                                    'Rekeningnummer' => '{bankAccountNumber}'
                                ]
                            ]
                        ],
                        [
                            "type" => "FormHtml",
                            "options" => ['html' => '<h3>Uittreksel bevolkingsregister</h3><p>U kunt een uittreksel uit het bevolkingsregister (de Basisregistratie personen) opvragen bij de gemeente waar u staat ingeschreven. Dit document bevat uw naam, geboortedatum en adres.</p>']
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/extractPersonalRecordsDatabase"
                        ],
                        [
                            "type" => "FormHtml",
                            "options" => ['html' => '<h3>Medische verklaring behandeltraject</h3><p>De <a title="Format verklaring arts op dus-i.nl " href="https://www.dus-i.nl/documenten/publicaties/2019/01/14/verklaring-behandelend-arts-borstprothesen-transvrouwen" target="_blank" rel="noopener" class="external">medische verklaring over het behandeltraject</a> dat u tot nu toe heeft gevolgd moet zijn ingevuld door de BIG-geregistreerde arts waar u in behandeling bent. Dit kan een huisarts of medisch specialist zijn die de hormonen voorschrijft en de behandeling begeleidt. De verklaring mag niet ouder zijn dan twee maanden.</p>']
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/proofOfMedicalTreatment"
                        ],
                        [
                            "type" => "FormHtml",
                            "options" => ['html' => '<h3>Medische verklaring van het type behandeling</h3><p>De <a title="Format verklaring type behandeling op dus-i.nl " href="https://www.dus-i.nl/subsidies/borstprothesen-transvrouwen/documenten/publicaties/2021/08/05/medische-verklaring-van-het-type-behandeling-borstprothesen-transvrouwen" target="_blank" rel="noopener" class="external">medische verklaring van het type behandeling</a> (operatie) dat zal worden uitgevoerd moet zijn ingevuld en ondertekend door een BIG-geregistreerde arts.</p>']
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/proofOfTypeOfMedicalTreatment"
                        ],
                        [
                            "type" => "FormHtml",
                            "options" => ['html' => '<h3>Ondertekenen</h3>']
                        ],
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/permissionToProcessPersonalData",
                            "label" => "Akkoord",
                            "options" => ['description' => 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld']
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
                            'lastName',
                            'street',
                            'houseNumber',
                            'postalCode',
                            'city',
                            'communicationPreference',
                            'phoneNumber',
                            'email',
                            'bankAccountHolder',
                            'bankAccountNumber'
                        ]
                    ]
                ],
                [
                    'type' => 'CustomPageControl',
                    'label' => 'Controleren, uploaden en verzenden',
                    'elements' => [$page3],
                    'options' => [
                        'required' => [
                            'permissionToProcessPersonalData',
                        ]
                    ]
                ]
            ]
        ];

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_V1_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($ui),
            'review_ui' => json_encode($ui)
        ]);
    }
}
