<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use stdClass;

class PCZMUIFormTableSeeder extends Seeder
{
    public const PCZM_STAGE1_V1_UUID = 'e6d5cd35-8c67-40c4-abc4-b1d6bf8afb97';

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

    private function buildViewSchema(): array
    {
        $filePath = __DIR__ . '/resources/pczm/viewschema.json';
        if(!file_exists($filePath)) {
            return [];
        }
        $json = file_get_contents($filePath);
        assert(is_string($json));
        return json_decode($json, true);
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

        $view_ui = $this->buildViewSchema();

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
