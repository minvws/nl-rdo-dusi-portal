<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use stdClass;

class PCZMUIFormTableSeeder extends Seeder
{
    public const PCZM_STAGE1_V1_UUID = 'E6D5CD35-8C67-40C4-ABC4-B1D6BF8AFB97';

    private function resolveFileReferencesInArray(array $array, string $basePath): void
    {
        foreach ($array as $value) {
            if ($value instanceof stdClass) {
                $this->resolveFileReferences($value, $basePath);
            } elseif (is_array($value)) {
                $this->resolveFileReferencesInArray($value, $basePath);
            }
        }
    }
    private function resolveFileReferences(stdClass $object, string $basePath): void
    {
        foreach (get_object_vars($object) as $key => $value) {
            if ($value instanceof stdClass) {
                $this->resolveFileReferences($value, $basePath);
            } elseif (is_array($value)) {
                $this->resolveFileReferencesInArray($value, $basePath);
            } elseif (is_string($value) && str_starts_with($value, 'file:')) {
                $path = $basePath . DIRECTORY_SEPARATOR . substr($value, 5);
                $object->$key = file_get_contents($path);
            }
        }
    }

    private function loadStep(int $step): stdClass
    {
        $json = file_get_contents(__DIR__ . '/resources/pczm/step' . $step . '.json');
        assert(is_string($json));
        $step = json_decode($json);
        assert($step instanceof stdClass);
        $this->resolveFileReferences($step, __DIR__ . '/resources/pczm');
        return $step;
    }

    private function loadAllOfStep(int $step): array
    {
        $stepFilePath = __DIR__ . '/resources/pczm/allOfStep' . $step . '.json';
        if(!file_exists($stepFilePath)) {
            return [];
        }
        $json = file_get_contents($stepFilePath);
        assert(is_string($json));
        $allOf = json_decode($json);

        return $allOf;
    }

    private function buildPage(int $step, string $label, array $required): stdClass
    {
        return (object)[
            'type' => 'CustomPageControl',
            'label' => $label,
            'elements' => [$this->loadStep($step)],
            'options' => (object)[
                'required' => $required,
                'allOf' => $this->loadAllOfStep($step)
            ]
        ];
    }

    public function run(): void
    {
        $ui = [
            'type' => 'CustomPageNavigationControl',
            'elements' => [
                $this->buildPage(
                    1,
                    'Start',
                    [
                    'permissionToProcessPersonalData'
                ]),
                $this->buildPage(2,
                    'Persoonsgegevens toevoegen',
                    [
                        "firstName",
                        "lastName",
                        "street",
                        "dateOfBirth",
                        "houseNumber",
                        "postalCode",
                        "city",
                        "country",
                        "phoneNumber",
                        "email",
                        "bankAccountHolder",
                        "bankAccountNumber"
                    ]
                ),
                $this->buildPage(3,
                    'Documenten toevoegen',
                    [
                        "certifiedEmploymentDocument",
                        "wiaDecisionDocument",
                        "isWiaDecisionPostponed",
                        "employmentContract",
                        "employmentFunction",
                        "employerKind",
                        "hasBeenWorkingAtJudicialInstitution",
                        "socialMedicalAssessment",
                        "hasPostCovidDiagnose",
                    ]
                ),
                $this->buildPage(4,
                    'Controleren en ondertekenen',
                    [
                        'truthfullyCompleted'
                ])
            ]
        ];

        $view_ui = [
            'sections' => [
                [
                    'title' => 'meta',
                    'elements' => [
                        [
                            "type"=>"string",
                            "field"=>"assessmentId"
                        ],[
                            "type"=>"string",
                            "field"=>"validFrom"
                        ],[
                            "type"=>"string",
                            "field"=>"validTo"
                        ]
                    ]
                ],
                [
                    'title' => 'personal',
                    'elements' => [
                        [
                            "type"=>"string",
                            "field"=>"firstName"
                        ],[
                            "type"=>"string",
                            "field"=>"infix"
                        ],[
                            "type"=>"string",
                            "field"=>"lastName"
                        ],[
                            "type"=>"date",
                            "field"=>"dateOfBirth"
                        ]
                    ]
                ],
                [
                    'title' => 'address',
                    'elements'=> [
                        [
                            "type"=>"string",
                            "field"=>"street"
                        ],[
                            "type"=>"string",
                            "field"=>"houseNumber"
                        ],[
                            "type"=>"string",
                            "field"=>"houseNumberSuffix"
                        ],[
                            "type"=>"string",
                            "field"=>"postalCode"
                        ],[
                            "type"=>"string",
                            "field"=>"city"
                        ],[
                            "type"=>"string",
                            "field"=>"country"
                        ],
                    ]
                ],
                [
                    'title' => 'contact',
                    'elements' => [
                        [
                            "type"=>"string",
                            "field"=>"phoneNumber"
                        ],[
                            "type"=>"string",
                            "field"=>"email"
                        ]
                    ]
                ],
                [
                    'title' => 'bank',
                    'elements' => [
                        [
                            "type"=>"string",
                            "field"=>"bankAccountNumber"
                        ],[
                            "type"=>"string",
                            "field"=>"bankAccountHolder"
                        ]
                    ]
                ],
                [
                    'title' => 'UWV',
                    'elements' => [
                        [
                            "type"=>"file",
                            "field"=>"certifiedEmploymentDocument"
                        ]
                    ]
                ],
                [
                    'title' => 'WIA',
                    'elements' => [
                        [
                            "type"=>"select",
                            "field"=>"isWiaDecisionPostponed"
                        ],[
                            "type"=>"file",
                            "field"=>"wiaDecisionPostponedLetter"
                        ],[
                            "type"=>"file",
                            "field"=>"wiaDecisionDocument"
                        ]
                    ]
                ],
                [
                    'title' => 'Werkgever',
                    'elements' => [
                        [
                            "type"=>"file",
                            "field"=>"employmentContract"
                        ],[
                            "type"=>"multiselect",
                            "field"=>"employmentFunction"
                        ],[
                            "type"=>"select",
                            "field"=>"employerKind"
                        ],[
                            "type"=>"file",
                            "field"=>"otherEmployerDeclarationFile"
                        ],[
                            "type"=>"select",
                            "field"=>"hasBeenWorkingAtJudicialInstitution"
                        ],[
                            "type"=>"string",
                            "field"=>"BIGNumberJudicialInstitution"
                        ]
                    ]
                ],
                [
                    'title' => 'Medisch',
                    'elements' => [
                        [
                            "type"=>"file",
                            "field"=>"socialMedicalAssessment"
                        ],[
                            "type"=>"select",
                            "field"=>"hasPostCovidDiagnose"
                        ],[
                            "type"=>"file",
                            "field"=>"doctorsCertificate"
                        ]
                    ]
                ],
            ]];

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::PCZM_STAGE1_V1_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
