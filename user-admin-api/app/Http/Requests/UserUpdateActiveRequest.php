<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use MinVWS\DUSi\Shared\User\Models\User;

class UserUpdateActiveRequest extends FormRequest
{
    protected $errorBag = 'user_update_active';

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
            'active_until' => [
                'nullable',
                'date_format:Y-m-d\\TH:i',
            ],
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
