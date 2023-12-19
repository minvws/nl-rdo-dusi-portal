<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use stdClass;

abstract class AbstractApplicationStageUISeeder extends Seeder
{
    public string $resourceDir;

    protected function resolveFileReferencesInArray(array $array, string $basePath): void
    {
        foreach ($array as $value) {
            if ($value instanceof stdClass) {
                $this->resolveFileReferences($value, $basePath);
            } elseif (is_array($value)) {
                $this->resolveFileReferencesInArray($value, $basePath);
            }
        }
    }
    protected function resolveFileReferences(stdClass $object, string $basePath): void
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

    protected function loadInputUiStepForStage(int $stage, int $step): stdClass
    {
        $json = file_get_contents($this->resourceDir . sprintf('/resources/input_ui/stage%d_step%d.json', $stage, $step));
        assert(is_string($json));
        $stageStepData = json_decode($json);
        assert($stageStepData instanceof stdClass);
        $this->resolveFileReferences($stageStepData, $this->resourceDir . '/resources/input_ui');
        return $stageStepData;
    }

    protected function loadAllOfStepForStage(int $stage, int $step): array
    {
        $stepFilePath = $this->resourceDir . sprintf('/resources/input_ui/stage%d_step%d_allOf.json', $stage, $step);
        if (!file_exists($stepFilePath)) {
            return [];
        }
        $json = file_get_contents($stepFilePath);
        assert(is_string($json));
        $allOf = json_decode($json);

        return $allOf;
    }

    protected function buildInputUiStep(int $step, string $label, array $required): stdClass
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
        $filePath = $this->resourceDir . '/resources/view_ui/stage1.json';
        if (!file_exists($filePath)) {
            return [];
        }
        $json = file_get_contents($filePath);
        assert(is_string($json));
        return json_decode($json, true);
    }
}
