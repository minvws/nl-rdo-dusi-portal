<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
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
        ];
    }
}
