<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use stdClass;

class PCZMApplicationStageUISeeder extends Seeder
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

    private function loadInputUiStepForStage(int $stage, int $step): stdClass
    {
        $json = file_get_contents(__DIR__ . sprintf('/resources/input_ui/stage%d_step%d.json', $stage, $step));
        assert(is_string($json));
        $stageStepData = json_decode($json);
        assert($stageStepData instanceof stdClass);
        $this->resolveFileReferences($stageStepData, __DIR__ . '/resources/input_ui');
        return $stageStepData;
    }

    private function loadAllOfStepForStage(int $stage, int $step): array
    {
        $stepFilePath = __DIR__ . sprintf('/resources/input_ui/stage%d_step%d_allOf.json', $stage, $step);
        if (!file_exists($stepFilePath)) {
            return [];
        }
        $json = file_get_contents($stepFilePath);
        assert(is_string($json));
        $allOf = json_decode($json);

        return $allOf;
    }

    private function buildInputUiStep(int $step, string $label, array $required): stdClass
    {
        return (object)[
            'type' => 'CustomPageControl',
            'label' => $label,
            'elements' => [$this->loadInputUiStepForStage(1, $step)],
            'options' => (object)[
                'required' => $required,
                'allOf' => $this->loadAllOfStepForStage(1, $step)
            ]
        ];
    }

    public function buildViewSchema(): array
    {
        $filePath = __DIR__ . '/resources/view_ui/stage1.json';
        if (!file_exists($filePath)) {
            return [];
        }
        $json = file_get_contents($filePath);
        assert(is_string($json));
        return json_decode($json, true);
    }

    public function run(): void
    {
        $inputUi = $this->buildInputUi();
        $viewUi = $this->buildViewSchema();

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::PCZM_STAGE1_V1_UUID,
            'subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_1_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($inputUi),
            'view_ui' => json_encode($viewUi)
        ]);
    }

    public function buildInputUi(): array
    {
        return [
            'type' => 'CustomPageNavigationControl',
            'elements' => [
                $this->buildInputUiStep(
                    1,
                    'Start',
                    [
                    ]
                ),
                $this->buildInputUiStep(
                    2,
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
                $this->buildInputUiStep(
                    3,
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
                $this->buildInputUiStep(
                    4,
                    'Controleren en ondertekenen',
                    [
                        'truthfullyCompleted'
                    ]
                )
            ]
        ];
    }
}
