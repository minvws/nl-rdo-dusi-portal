<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ApplicationExportRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date_from' => 'date',
            'date_to' => 'date',
        ];
    }
}
