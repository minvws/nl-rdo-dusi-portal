<?php

namespace App\Http\Resources;

use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'metadata' => $this->createMetadata(),
            'dataSchema' => $this->createDataSchema(),
            'uiSchema' => $this->createUISchema(),
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
                'title' => $this->subsidy->title
            ]
        ];
    }

    private function createDataSchema(): array
    {
        $result['type'] = 'object';
        $result['properties'] = [];

        $required = [];
        foreach ($this->fields as $field) {
            $result['properties'][$field->id] = $this->createFieldDataSchema($field);
            if ($field->is_required) {
                $required[] = $field->id;
            }
        }

        if (count($required) > 0) {
            $result['required'] = $required;
        }

        return $result;
    }

    private function createFieldDataSchema(Field $field): array
    {
        return [
            'type' => 'string',
            'title' => $field->label
        ];
    }

    private function createUISchema(): array
    {
        $result = [
            'type' => 'VerticalLayout',
            'elements' => []
        ];

        foreach ($this->fields as $field) {
            $result['elements'][] = $this->createFieldUISchema($field);
        }

        return $result;
    }

    private function createFieldUISchema(Field $field): array
    {
        return [
            'type' => 'Control',
            'scope' => "#/properties/{$field->id}"
        ];
    }
}
