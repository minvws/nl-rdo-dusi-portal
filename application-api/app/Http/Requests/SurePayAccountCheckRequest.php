<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SurePayAccountCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bankAccountHolder' => 'string|required',
            'bankAccountNumber' => 'string|required'
        ];
    }
}
