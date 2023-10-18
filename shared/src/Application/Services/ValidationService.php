<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\FileUploadRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\RequiredConditionRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\SurePayValidationRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\Validator;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidatorFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class ValidationService
{
    public function __construct(
        protected ValidatorFactory $validatorFactory,
        protected SurePayService $surePayService,
    ) {
    }

    /**
     * @param array<int|string, FieldValue> $fieldValues
     */
    public function getValidator(ApplicationStage $applicationStage, array $fieldValues, bool $submit): Validator
    {
        return $this->validatorFactory->getValidator(
            applicationStage: $applicationStage,
            fieldValues: $fieldValues,
            data: $this->getFieldValuesData($fieldValues),
            rules: $this->getFieldValuesRules($applicationStage->subsidyStage->stage, $fieldValues, $submit),
        );
    }

    /**
     * @return array<string|ValidationRule>
     */
    protected function getFieldValidationRules(int $stage, Field $field, bool $submit): array
    {
        $rules = [];
        if ($submit && $field->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        if ($submit && isset($field->required_condition)) {
            $rules[] = new RequiredConditionRule($stage, $field->required_condition);
        }

        return [...$rules , ...match ($field->type) {
            FieldType::Checkbox => [...$this->getBooleanFieldRules($field, $submit)],
            FieldType::CustomBankAccount => [...$this->getCustomBankAccountFieldRules($field)],
            FieldType::CustomCountry => [],
            FieldType::CustomPostalCode => [],
            FieldType::Date => [],
            FieldType::Multiselect => ['array', ...$this->getSelectFieldRules($field)],
            FieldType::Select => ['string', ...$this->getSelectFieldRules($field)],
            FieldType::Text => [...$this->getTextFieldRules($field)],
            FieldType::TextArea => [...$this->getTextFieldRules($field)],
            FieldType::TextEmail => ['email:strict', ...$this->getTextFieldRules($field)],
            FieldType::TextTel => [...$this->getTextFieldRules($field)],
            FieldType::TextNumeric => [...$this->getNumericFieldRules($field)],
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
            $data[$fieldValue->field->code] = $fieldValue->value;
        }

        return $data;
    }
    /**
     * @param FieldValue[] $fieldValues
     */
    protected function getFieldValuesRules(int $stage, array $fieldValues, bool $submit): array
    {
        $rules = [];

        foreach ($fieldValues as $fieldValue) {
            $rules[$fieldValue->field->code] = $this->getFieldValidationRules($stage, $fieldValue->field, $submit);
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
            FieldType::TextTel
            ], true)
        ) {
            return [];
        }

        $rules = [
            'string',
        ];

        $maxLength = $field->params['minLength'] ?? null;
        if (!empty($maxLength)) {
            $rules[] = 'min:' . $field->params['minLength'];
        }

        $maxLength = $field->params['maxLength'] ?? null;
        if (!empty($maxLength)) {
            $rules[] = 'max:' . $field->params['maxLength'];
        }

        return $rules;
    }

    protected function getNumericFieldRules(Field $field): array
    {
        if ($field->type !== FieldType::TextNumeric) {
            return [];
        }

        $rules = [
            'integer'
        ];

        $maxLength = $field->params['minimum'] ?? null;
        if (!empty($maxLength)) {
            $rules[] = 'min:' . $field->params['minimum'];
        }

        $maxLength = $field->params['maximum'] ?? null;
        if (!empty($maxLength)) {
            $rules[] = 'max:' . $field->params['maximum'];
        }

        return $rules;
    }

    protected function getBooleanFieldRules(Field $field, bool $submit): array
    {
        if ($field->type !== FieldType::Checkbox) {
            return [];
        }

        $rules = [
            'boolean',
        ];

        if ($submit && $field->is_required) {
            $rules[] = 'accepted';
        }

        return $rules;
    }

    protected function getCustomBankAccountFieldRules(Field $field): array
    {
        if ($field->type !== FieldType::CustomBankAccount) {
            return [];
        }
        return [ new SurePayValidationRule($this->surePayService) ];
    }
}
