<?php

namespace App\Http\Requests;

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
