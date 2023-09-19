<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class FileValidator
{

    public function __construct(
        protected ValidatorFactory $fd,
    )
    {
    }

    public function getValidator(Field $field, UploadedFile $file): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'required',
            'file',
        ];

        $maxSize = $field->params['maxSize'] ?? null;
        if ($maxSize !== null) {
            $rules[] = 'size:' . $maxSize;
        }

        $mimeType = $field->params['accept'] ?? null;
        if ($mimeType !== null) {
            $rules[] = 'mimetypes:' . $mimeType;
        }

        // Add as last so size and mime type are checked first
        $rules[] = 'clamav';

        Log::info('upload field params: ' . json_encode($field->params));

        return new \Illuminate\Validation\Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'file' => $file,
            ],
            rules: [
                'file' => $rules,
            ],
        );
    }

}
