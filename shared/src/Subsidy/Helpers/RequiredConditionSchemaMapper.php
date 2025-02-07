<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Helpers;

use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\IsEmptyCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class RequiredConditionSchemaMapper
{
    public function mapConditionAndFieldToDataScheme(Condition $condition, Field $field): array
    {
        $conditions = $this->mapConditionToSchema($condition);

        return $this->getIfThenSchema(
            $conditions,
            [$field->code]
        );
    }

    protected function mapConditionToSchema(Condition $condition): array
    {

        return match ($condition::class) {
            AndCondition::class => $this->mapAndConditionToSchema($condition),
            ComparisonCondition::class => $this->mapComparisonConditionToSchema($condition),
            InCondition::class => $this->mapInConditionToSchema($condition),
            IsEmptyCondition::class => $this->mapIsEmptyConditionToSchema($condition),
            OrCondition::class => $this->mapOrConditionToSchema($condition),
            default => throw new \Exception('Unknown condition type: ' . $condition::class),
        };
    }

    protected function mapAndConditionToSchema(AndCondition $condition): array
    {
        $properties = [];

        foreach ($condition->conditions as $subCondition) {
            $properties[] = $this->mapConditionToSchema($subCondition);
        }

        return $this->getAllOfSchema($properties);
    }

    private function mapComparisonConditionToSchema(ComparisonCondition $comparisonCondition): array
    {
        $condition = match ($comparisonCondition->operator) {
            Operator::Equal, Operator::Identical => [
                'const' => $comparisonCondition->value,
            ],
            Operator::NotEqual, Operator::NotIdentical => [
                'not' => $comparisonCondition->value,
            ],
            Operator::GreaterThan => [
                'exclusiveMinimum' => $comparisonCondition->value,
            ],
            Operator::GreaterThanOrEqualTo => [
                'minimum' => $comparisonCondition->value,
            ],
            Operator::LessThan => [
                'exclusiveMaximum' => $comparisonCondition->value,
            ],
            Operator::LessThanOrEqualTo => [
                'maximum' => $comparisonCondition->value,
            ],
        };

        return $this->getConditionSchema(
            [$comparisonCondition->fieldCode],
            [$comparisonCondition->fieldCode => $condition]
        );
    }

    private function mapInConditionToSchema(InCondition $condition): array
    {
        return $this->getConditionSchema([], [
            $condition->fieldCode => [
                'enum' => $condition->values,
            ],
        ]);
    }

    private function mapIsEmptyConditionToSchema(IsEmptyCondition $condition): array
    {
        return $this->getConditionSchema([], [
            $condition->fieldCode => [
                'maxLength' => 0
            ],
        ]);
    }

    private function mapOrConditionToSchema(OrCondition $condition): array
    {
        $properties = [];

        foreach ($condition->conditions as $subCondition) {
            $properties[] = $this->mapConditionToSchema($subCondition);
        }

        return $this->getAnyOfSchema($properties);
    }

    protected function getIfThenSchema(array $conditions, array $thenRequiredFields): array
    {
        return [
            'if' => $conditions,
            'then' => [
                'required' => $thenRequiredFields,
            ],
        ];
    }

    protected function getAnyOfSchema(array $properties): array
    {
        return [
            'anyOf' => $properties,
        ];
    }

    protected function getAllOfSchema(array $properties): array
    {
        return [
            'allOf' => $properties,
        ];
    }

    protected function getConditionSchema(array $required = [], array $properties = []): array
    {
        return array_filter([
            'required' => $required,
            'properties' => $properties,
        ]);
    }
}
