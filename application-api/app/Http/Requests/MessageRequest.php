<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'date_from' => 'date',
            'date_to' => 'date',
            'statuses' => 'string',
            'subsidies' => 'string',
        ];

        return $rules;
    }
}
