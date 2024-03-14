<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;
use MinVWS\DUSi\Shared\User\Enums\Role;

class UserRoleAttachRequest extends FormRequest
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
        $subsidyRules = ['nullable', 'string', 'exists:' . Connection::APPLICATION . '.subsidies,id'];

        if (app()->environment(['production', 'acceptance'])) {
            array_unshift($subsidyRules, 'required_unless:role,' . Role::UserAdmin->value);
        }

        return [
            'role' => ['required', 'string', 'exists:roles,name'],
            'subsidy_id' => $subsidyRules,
        ];
    }
}
