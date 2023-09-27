<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use MinVWS\DUSi\Shared\User\Models\User;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:strict',
                'max:255',
                'unique:users,email',
            ],
            'organisation_id' => ['required', 'string', 'exists:organisations,id'],
        ];
    }
}
