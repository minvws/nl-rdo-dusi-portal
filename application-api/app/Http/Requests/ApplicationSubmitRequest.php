<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;

class ApplicationSubmitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data' => 'string|required',
            'status' => [
                'string',
                'required',
                Rule::in([ApplicationStatus::Draft->value, ApplicationStatus::Submitted->value]),
            ],
        ];
    }
}
