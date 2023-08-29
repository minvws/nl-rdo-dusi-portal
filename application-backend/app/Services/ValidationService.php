<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use MinVWS\DUSi\Application\Backend\Services\Validation\Rules\FileUploadRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\Validator;
use MinVWS\DUSi\Application\Backend\Services\Validation\ValidatorFactory;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class ValidationService
{
    public function __construct(
        protected ValidatorFactory $validatorFactory,
    ) {
    }

    /**
     * @param ApplicationStageVersion $applicationStageVersion
     * @param FieldValue[] $fieldValues
     * @return Validator
     */
    public function getValidator(ApplicationStageVersion $applicationStageVersion, array $fieldValues): Validator
    {
        return $this->validatorFactory->getValidator(
            applicationStageVersion: $applicationStageVersion,
            fieldValues: $fieldValues,
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

        return [...$rules , ...match ($field->type) {
            FieldType::Checkbox => ['boolean'],
            FieldType::CustomBankAccount => [],
            FieldType::CustomCountry => [],
            FieldType::CustomPostalCode => [],
            FieldType::Date => [],
            FieldType::Multiselect => ['array', ...$this->getSelectFieldRules($field)],
            FieldType::Select => [...$this->getSelectFieldRules($field)],
            FieldType::Text => [...$this->getTextFieldRules($field)],
            FieldType::TextArea => [...$this->getTextFieldRules($field)],
            FieldType::TextEmail => ['email:strict', ...$this->getTextFieldRules($field)],
            FieldType::TextTel => [...$this->getTextFieldRules($field)],
            FieldType::TextNumeric => [...$this->getTextFieldRules($field)],
            FieldType::TextUrl => [],
            FieldType::Upload => [new FileUploadRule($field)],
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
        if (
            !in_array($field->type, [
            FieldType::Text,
            FieldType::TextArea,
            FieldType::TextNumeric,
            FieldType::TextTel
            ], true)
        ) {
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
