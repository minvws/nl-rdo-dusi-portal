<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use stdClass;

class BTVUIFormTableSeeder extends Seeder
{
    public const BTV_STAGE1_V1_UUID = '72475863-7987-4375-94d7-21e04ff6552b';

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
        $json = file_get_contents(__DIR__ . '/resources/btv/step' . $step . '.json');
        assert(is_string($json));
        $step = json_decode($json);
        assert($step instanceof stdClass);
        $this->resolveFileReferences($step, __DIR__ . '/resources/btv');
        return $step;
    }

    private function buildPage(int $step, array $required): stdClass
    {
        return (object)[
            'type' => 'CustomPageControl',
            'label' => 'Start',
            'elements' => [$this->loadStep($step)],
            'options' => (object)[
                'required' => $required
            ]
        ];
    }

    private function buildViewSchema(): array
    {
        $filePath = __DIR__ . '/resources/btv/viewschema.json';
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
                $this->buildPage(1, [
                    'permissionToProcessPersonalData'
                ]),
                $this->buildPage(2, [
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
                ]),
                $this->buildPage(3, [
                    "bankStatement",
                    "extractPersonalRecordsDatabase",
                    "proofOfMedicalTreatment",
                    "proofOfTypeOfMedicalTreatment"
                ]),
                $this->buildPage(4, [
                    'truthfullyCompleted'
                ])
            ]
        ];

        $view_ui = $this->buildViewSchema();

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE1_V1_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
