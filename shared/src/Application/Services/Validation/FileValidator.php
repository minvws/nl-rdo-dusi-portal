<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator as LaravelValidator;
use MinVWS\DUSi\Shared\Application\Services\ClamAv\ClamAvService;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ClamAv;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Psr\Log\LoggerInterface;

class FileValidator
{
    private ?LaravelValidator $validator;

    public function __construct(
        private ClamAvService $clamAvService,
        private LoggerInterface $logger,
        private Translator $translator,
    ) {
        $this->validator = null;
    }

    public function getValidator(Field $field, UploadedFile $file): ValidatorContract
    {
        if ($this->validator === null) {
            $this->validator = $this->createValidator($field, $file);
        }

        return $this->validator;
    }

    public function getRules(Field $field): array
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

        return $rules;
    }

    private function createValidator(Field $field, UploadedFile $file): LaravelValidator
    {
        $rules = $this->getRules($field);

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

    public function failsOnMimetype(): bool
    {
        if ($this->validator === null) {
            return false;
        }

        return collect(array_keys($this->validator->failed()['file']))
            ->filter(fn(int|string $rule) => strpos(strtolower((string)$rule), 'mimetypes') !== false)
            ->isNotEmpty();
    }
}
