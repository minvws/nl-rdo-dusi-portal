<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use MinVWS\DUSi\Assessment\API\Rules\SortRule;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;

class ApplicationRequest extends FormRequest
{
    /*
     * We convert camelCased data to snake_case
     */
    protected function prepareForValidation(): void
    {
        $keys = array_keys($this->rules());

        foreach ($keys as $key) {
            $camelCasedKey = Str::camel($key);

            if ($key !== $camelCasedKey && $this->has($camelCasedKey)) {
                $value = $this->get($camelCasedKey);
                $this->getInputSource()->set($key, $value);
                $this->getInputSource()->remove($camelCasedKey);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<int, Enum|ValidationRule|Rule|SortRule|string>>
     */
    public function rules(): array
    {
        $sortableColumns = [
            'updated_at',
            'final_review_deadline',
        ];

        return [
            'sort' => ['string', new SortRule($sortableColumns)],
            'reference' => 'string',
            'date_from' => 'date',
            'date_to' => 'date',
            'status' => 'array',
            'status.*' => [new Enum(ApplicationStatus::class)],
            'subsidy' => 'array',
            'subsidy.*' => 'string',
            'phase' => 'array',
            'phase.*' => 'string',
            'date_last_modified_from' => 'date',
            'date_last_modified_to' => 'date',
            'date_final_review_deadline_from' => 'date',
            'date_final_review_deadline_to' => 'date',
            'per_page' => ['nullable', 'integer', 'between:1,100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
