<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationSubmitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data' => 'string|required'
        ];
    }
}
