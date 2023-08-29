<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserResetCredentialsRequest extends FormRequest
{
    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'user_reset_credentials';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('resetCredentials', $this->route('user')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reset_password' => ['sometimes', 'accepted'],
            'reset_2fa' => ['sometimes', 'accepted'],
        ];
    }
}
