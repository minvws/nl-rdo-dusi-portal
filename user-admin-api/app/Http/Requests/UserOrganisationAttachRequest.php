<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserOrganisationAttachRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organisation_id' => ['required', 'string', 'exists:organisations,id'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
