<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'application_title' => 'string',
            'date_from' => 'date',
            'date_to' => 'date',
            'status' => 'string',
            'subsidy' => 'string',
            'date_last_modified_from' => 'date',
            'date_last_modified_to' => 'date',
            'date_final_review_deadline_from' => 'date',
            'date_final_review_deadline_to' => 'date',
        ];

        return $rules;
    }
}
