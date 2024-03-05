<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

class UserRoleDetachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'exists:roles,name'],
            'subsidy_id' => ['nullable', 'string', 'exists:' . Connection::APPLICATION . '.subsidies,id'],
        ];
    }
}
