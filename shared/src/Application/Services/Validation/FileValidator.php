<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator as LaravelValidator;
use MinVWS\DUSi\Shared\Application\Services\Clamav\ClamAvService;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ClamAv;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Psr\Log\LoggerInterface;

readonly class FileValidator
{
    public function __construct(
        private ClamAvService $clamAvService,
        private LoggerInterface $logger,
        private Translator $translator,
    ) {
    }

    public function getValidator(Field $field, UploadedFile $file): ValidatorContract
    {
        $rules = [
            'required',
            'file',
        ];

        $maxFileSize = $field->params['maxFileSize'] ?? null;
        if ($maxFileSize !== null) {
            $rules[] = 'max:' . $maxFileSize;
        }

        $mimeTypes = $field->params['mimeTypes'] ?? [];
        if (is_array($mimeTypes) && count($mimeTypes) > 0) {
            $rules[] = 'mimetypes:' . implode(',', $mimeTypes);
        }

        // Add as last so size and mime type are checked first
        $rules[] = new ClamAv($this->clamAvService, $this->logger);

        return new LaravelValidator(
            translator: $this->translator,
            data: [
                'file' => $file,
            ],
            rules: [
                'file' => $rules,
            ],
        );
    }
}
