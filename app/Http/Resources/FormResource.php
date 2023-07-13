<?php

namespace App\Http\Resources;

use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\FieldType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'metadata' => $this->createMetadata(),
            'dataSchema' => $this->createDataSchema(),
            'uiSchema' => $this->publishedUI?->ui,
            '_links' => [
                'submit' => ['href' => route('api.form-submit', $this->id)]
            ],
        ];
    }

    private function createMetadata(): array
    {
        return [
            'id' => $this->id,
            'subsidy' => [
                'id' => $this->subsidy->id,
                'title' => $this->subsidy->title,
                'description' => $this->subsidy->description,
                'validFrom' => $this->subsidy->valid_from->format('Y-m-d'),
                'validTo' => $this->subsidy->valid_to?->format('Y-m-d')
            ]
        ];
    }

    private function createDataSchema(): array
    {
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

        if (!empty($field->description)) {
            $result['description'] = $field->description;
        }

        if ($field->type === FieldType::Select) {
            $result['enum'] = $field->params['options'];
        } elseif ($field->type === FieldType::Upload) {
            $result['file'] = true;
        } elseif ($field->type === FieldType::CustomBankAccount) {
            $result['iban'] = true;
        } elseif ($field->type === FieldType::TextTel) {
            $result['tel'] = true;
        } elseif ($field->type === FieldType::Checkbox) {
            $result['const'] = true;
        } elseif ($field->type === FieldType::TextEmail) {
            $result['format'] = 'email';
        }

        return $result;
    }
}
