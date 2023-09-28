<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Helpers;

use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;

class SubsidyStageDataSchemaBuilder
{
    public function buildDataSchema(SubsidyStage $subsidyStage): array
    {
        $result = [];
        $result['type'] = 'object';
        $result['properties'] = [];

        $required = [];
        foreach ($subsidyStage->fields as $field) {
            $result['properties'][$field->code] = $this->createFieldDataSchema($field);
            if ($field->is_required) {
                $required[] = $field->code;
            }
        }

        if (count($required) > 0) {
            $result['required'] = $required;
        }

        return $result;
    }

    /**
     * @param array $result
     * @param Field $field
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function addFieldOptionsForType(array &$result, Field $field): void
    {
        switch ($field->type) {
            case FieldType::Checkbox:
                $result['const'] = true;
                break;
            case FieldType::CustomBankAccount:
                $result['iban'] = true;
                break;
            case FieldType::CustomCountry:
                // Currently nothing extra
                break;
            case FieldType::CustomPostalCode:
                // Currently nothing extra
                break;
            case FieldType::Date:
                $result['format'] = 'date';
                break;
            case FieldType::Multiselect:
                $result['items'] = [
                    'type' => 'string',
                    'enum' => $field->params['options']
                ];
                break;
            case FieldType::Select:
                $result['enum'] = $field->params['options'];
                break;
            case FieldType::Text:
                $result = array_merge($result, $this->getStringValidationOptions($field));
                break;
            case FieldType::TextArea:
                $result = array_merge($result, $this->getStringValidationOptions($field));
                break;
            case FieldType::TextEmail:
                $result['format'] = 'email';
                $result = array_merge($result, $this->getStringValidationOptions($field));
                break;
            case FieldType::TextTel:
                $result['tel'] = true;
                $result = array_merge($result, $this->getStringValidationOptions($field));
                break;
            case FieldType::TextNumeric:
                $result['minimum'] = 0;
                $result = array_merge($result, $this->getNumberValidationOptions($field));
                break;
            case FieldType::TextUrl:
                // Currently nothing extra
                break;
            case FieldType::Upload:
                $result['items'] = [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'string'],
                        'name' => ['type' => 'string'],
                        'mimeType' => ['type' => 'string'],
                    ],
                    'required' => ['id']
                ];
                $result['file'] = true;
                break;
        }
    }

    private function createFieldDataSchema(Field $field): array
    {
        $type = match ($field->type) {
            FieldType::TextNumeric => 'integer',
            FieldType::Checkbox => 'boolean',
            FieldType::Upload => 'array',
            FieldType::Multiselect => 'array',
            default => 'string'
        };

        $result = [
            'type' => $type,
            'title' => $field->title,
        ];
        if (isset($field->params['default'])) {
            $result['default'] = $field->params['default'];
        }

        if (!empty($field->description)) {
            $result['description'] = $field->description;
        }

        $this->addFieldOptionsForType($result, $field);

        return $result;
    }

    /**
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema4.ts
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema7.ts
     * @param Field $field
     * @return array
     */
    protected function getNumberValidationOptions(Field $field): array
    {
        return array_filter([
            'multipleOf' => $field->params['multipleOf'] ?? null,
            'maximum' => $field->params['maximum'] ?? null,
            'exclusiveMaximum' => $field->params['exclusiveMaximum'] ?? null,
            'minimum' => $field->params['minimum'] ?? null,
            'exclusiveMinimum' => $field->params['exclusiveMinimum'] ?? null,
        ]);
    }

    /**
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema4.ts
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema7.ts
     * @param Field $field
     * @return array
     */
    protected function getStringValidationOptions(Field $field): array
    {
        return array_filter([
            'maxLength' => $field->params['maxLength'] ?? null,
            'minLength' => $field->params['minLength'] ?? null,
            'pattern' => $field->params['pattern'] ?? null,
        ]);
    }

    /**
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema4.ts
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema7.ts
     * @param Field $field
     * @return array
     */
    protected function getArrayValidationOptions(Field $field): array
    {
        return array_filter([
            'maxItems' => $field->params['maxItems'] ?? null,
            'minItems' => $field->params['minItems'] ?? null,
            'uniqueItems' => $field->params['uniqueItems'] ?? null,
        ]);
    }

    /**
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema4.ts
     * @see https://github.com/eclipsesource/jsonforms/blob/master/packages/core/src/models/jsonSchema7.ts
     * @param Field $field
     * @return array
     */
    protected function getObjectValidationOptions(Field $field): array
    {
        return array_filter([
            'maxProperties' => $field->params['maxProperties'] ?? null,
            'minProperties' => $field->params['minProperties'] ?? null,
        ]);
    }
}
