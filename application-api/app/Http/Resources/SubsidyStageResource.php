<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

/**
 * @property mixed $publishedUI
 * @property mixed $id
 * @property mixed $subsidyVersion
 * @property mixed $fields
 */
class SubsidyStageResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            'metadata' => $this->createMetadata(),
            'dataschema' => $this->createDataSchema(),
            'uischema' => $this->publishedUI?->input_ui
        ];
    }

    private function createMetadata(): array
    {
        return [
            'id' => $this->id,
            'subsidy' => [
                'id' => $this->subsidyVersion->subsidy->id,
                'title' => $this->subsidyVersion->subsidy->title,
                'description' => $this->subsidyVersion->subsidy->description,
                'validFrom' => $this->subsidyVersion->subsidy->valid_from->format('Y-m-d'),
                'validTo' => $this->subsidyVersion->subsidy->valid_to?->format('Y-m-d')
            ]
        ];
    }

    private function createDataSchema(): array
    {
        $result = [];
        $result['type'] = 'object';
        $result['properties'] = [];

        $required = [];
        foreach ($this->fields as $field) {
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

    private function addFieldOptionsForType(array &$result, Field $field): void
    {
        switch ($field->type) {
            case FieldType::Select:
                $result['enum'] = $field->params['options'];
                break;
            case FieldType::Upload:
                $result['file'] = true;
                break;
            case FieldType::CustomBankAccount:
                $result['iban'] = true;
                break;
            case FieldType::TextTel:
                $result['tel'] = true;
                break;
            case FieldType::Checkbox:
                $result['const'] = true;
                break;
            case FieldType::TextEmail:
                $result['format'] = 'email';
                break;
            case FieldType::Date:
                $result['format'] = 'date';
                break;
            default:
        }
    }

    private function createFieldDataSchema(Field $field): array
    {

        $type = match ($field->type) {
            FieldType::TextNumeric => 'integer',
            FieldType::Checkbox => 'boolean',
            default => 'string'
        };

        $result = [
            'type' => $type,
            'title' => $field->title,
        ];

        if ($type === 'integer') {
            $result['minimum'] = 0;
        }

        if (!empty($field->description)) {
            $result['description'] = $field->description;
        }

        $this->addFieldOptionsForType($result, $field);

        return $result;
    }
}
