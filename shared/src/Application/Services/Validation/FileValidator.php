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
    private const FILE_FIELD_NAME = 'file';

    public function __construct(
        private ClamAvService $clamAvService,
        private LoggerInterface $logger,
        private Translator $translator,
    ) {
    }

    public function getValidator(Field $field, UploadedFile $file): ValidatorContract
    {
        $rules = $this->getRules($field);

        return new LaravelValidator(
            translator: $this->translator,
            data: [
                self::FILE_FIELD_NAME => $file,
            ],
            rules: [
                self::FILE_FIELD_NAME => $rules,
            ],
        );
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

    /**
     * Check if the validator fails on the mimetype rule
     *
     * If the validator did not run yet, it will run the validation rules
     * This is done under the hood by calling the errors() method
     *
     * @param ValidatorContract $validator
     * @return bool True if the validator fails on the mimetype rule
     */
    public function failsOnMimetype(ValidatorContract $validator): bool
    {
        if ($validator->errors()->isEmpty()) {
            return false;
        }

        $failedRules = $validator->failed();
        if (!array_key_exists(self::FILE_FIELD_NAME, $failedRules)) {
            return false;
        }

        return array_key_exists('Mimetypes', $failedRules[self::FILE_FIELD_NAME]);
    }
}
