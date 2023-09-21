<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Validation;

use Illuminate\Http\UploadedFile;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Application\Backend\Rules\ClamAv;
use MinVWS\DUSi\Application\Backend\Services\Clamav\ClamAvService;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\Validator as LaravelValidator;
use Psr\Log\LoggerInterface;

readonly class FileValidator
{
    public function __construct(
        private ClamAvService $clamAvService,
        private LoggerInterface $logger,
    ) {
    }

    public function getValidator(Field $field, UploadedFile $file): ValidatorContract
    {
        $rules = [
            'required',
            'file',
        ];

        $maxSize = $field->params['maxSize'] ?? null;
        if ($maxSize !== null) {
            $rules[] = 'size:' . $maxSize;
        }

        $mimeTypes = $field->params['mimeTypes'] ?? [];
        if (is_array($mimeTypes) && count($mimeTypes) > 0) {
            $rules[] = 'mimetypes:' . implode(',', $mimeTypes);
        }

        // Add as last so size and mime type are checked first
        $rules[] = new ClamAv($this->clamAvService, $this->logger);

        return new LaravelValidator(
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
