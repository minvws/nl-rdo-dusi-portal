<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Requests;

use MinVWS\DUSi\Application\API\Models\Application;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationUploadFileRequest extends FormRequest
{
    public function rules(): array
    {
        $application = $this->input('application');
        assert($application instanceof Application);

        // TODO: validate fieldCode (only file uploads)

        return [
            'fieldCode' => 'string|required',
            'file' => 'file|required'
        ];
    }
}
