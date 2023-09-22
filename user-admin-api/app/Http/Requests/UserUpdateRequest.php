<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use MinVWS\DUSi\User\Admin\API\Models\User;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->getRouteParamUser()) ?? false;
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
                'unique:users,email,' . $this->getRouteParamUser()->id,
            ],
            'organisation_id' => ['required', 'string', 'exists:organisations,id'],
        ];
    }

    public function getRouteParamUser(): User
    {
        $user = $this->route('user');
        if (!($user instanceof User)) {
            throw new \RuntimeException('User not found');
        }

        return $user;
    }
}
