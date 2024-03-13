<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Services\FieldHooks\FieldHook;
use TypeError;

class ApplicationFieldHookService
{
    public function findAndExecuteHooks(array $fieldValues, ApplicationStage $applicationStage): array
    {
        return array_map(function (FieldValue $fieldValue) use ($fieldValues, $applicationStage) {
            return $this->findAndExecuteHook($fieldValue, $fieldValues, $applicationStage);
        }, $fieldValues);
    }

    private function findAndExecuteHook(
        FieldValue $fieldValue,
        array $fieldValues,
        ApplicationStage $applicationStage
    ): FieldValue {
        if ($this->fieldHookExists($fieldValue, $applicationStage)) {
            return $this->executeFieldHook($fieldValue, $fieldValues, $applicationStage);
        }
        return $fieldValue;
    }

    private function fieldHookExists(FieldValue $fieldValue, ApplicationStage $applicationStage): bool
    {
        $className = $this->getFieldHookClassName($fieldValue);
        if (!class_exists($className)) {
            return false;
        }

        try {
            $classInstance = $this->createFieldHookClassInstance($className);
        } catch (TypeError $e) {
            return false;
        }

        return $classInstance->isHookActive($applicationStage);
    }

    private function executeFieldHook(
        FieldValue $fieldValue,
        array $fieldValues,
        ApplicationStage $applicationStage
    ): FieldValue {
        $className = $this->getFieldHookClassName($fieldValue);
        $classInstance = $this->createFieldHookClassInstance($className);

        return $classInstance->run($fieldValue, $fieldValues, $applicationStage);
    }

    private function createFieldHookClassInstance(string $className): FieldHook
    {
        $classInstance = new $className();

        assert($classInstance instanceof FieldHook);

        return $classInstance;
    }

    public function getFieldHookClassName(FieldValue $fieldValue): string
    {
        return "MinVWS\\DUSi\\Shared\\Application\\Services\\FieldHooks\\" . ucfirst($fieldValue->field->code);
    }
}
