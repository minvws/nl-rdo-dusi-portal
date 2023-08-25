<?php

namespace MinVWS\DUSi\Application\Backend\Services;


use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Rule;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class ValidationService
{
    public function __construct()
    {
    }

    /**
     * @param FieldValue[] $fieldValues
     * @return Validator
     */
    public function getValidator(array $fieldValues): Validator
    {
        ray('fieldValues', $fieldValues);
        ray('getValidator', $this->getFieldValuesRules($fieldValues), $this->getFieldValuesData($fieldValues));

        return new \Illuminate\Validation\Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: $this->getFieldValuesData($fieldValues),
            rules: $this->getFieldValuesRules($fieldValues),
        );
    }

    /**
     * @param Field $field
     * @return array<string|ValidationRule>
     */
    protected function getFieldValidationRules(Field $field): array
    {
        $rules = [];

        if ($field->is_required) {
            $rules[] = 'required';
        }

        return [...$rules , ...match($field->type) {
            FieldType::Checkbox => ['boolean'],
            FieldType::CustomBankAccount => [],
            FieldType::CustomCountry => [],
            FieldType::CustomPostalCode => [],
            FieldType::Multiselect => ['array', ...$this->getSelectFieldRules($field)],
            FieldType::Select => [...$this->getSelectFieldRules($field)],
            FieldType::Text => [...$this->getTextFieldRules($field)],
            FieldType::TextArea => [...$this->getTextFieldRules($field)],
            FieldType::TextEmail => ['email:strict', ...$this->getTextFieldRules($field)],
            FieldType::TextTel => [...$this->getTextFieldRules($field)],
            FieldType::TextNumeric => [...$this->getTextFieldRules($field)],
            FieldType::TextUrl => [],
            FieldType::Upload => [],
        }];
    }

    /**
     * @param FieldValue[] $fieldValues
     */
    protected function getFieldValuesData(array $fieldValues): array
    {
        $data = [];

        foreach ($fieldValues as $fieldValue) {
            $data[$fieldValue->field->id] = $fieldValue->value;
        }

        return $data;
    }
    /**
     * @param FieldValue[] $fieldValues
     */
    protected function getFieldValuesRules(array $fieldValues): array
    {
        $rules = [];

        foreach ($fieldValues as $fieldValue) {
            $rules[$fieldValue->field->id] = $this->getFieldValidationRules($fieldValue->field);
        }

        return $rules;
    }

    protected function getSelectFieldRules(Field $field): array
    {
        if (!in_array($field->type, [FieldType::Select, FieldType::Multiselect], true)) {
            return [];
        }

        $rules = [];

        $selectOptions = $field->params['options'] ?? [];
        if (!empty($selectOptions)) {
            $rules[] = Rule::in($selectOptions);
        }

        return $rules;
    }

    protected function getTextFieldRules(Field $field): array
    {
        if (!in_array($field->type, [FieldType::Text, FieldType::TextArea, FieldType::TextNumeric, FieldType::TextTel], true)) {
            return [];
        }

        $rules = [];

        $maxLength = $field->params['maxLength'] ?? null;
        if (!empty($maxLength)) {
            $rules[] = 'max:' . $field->params['maxLength'];
        }

        return $rules;
    }

}
