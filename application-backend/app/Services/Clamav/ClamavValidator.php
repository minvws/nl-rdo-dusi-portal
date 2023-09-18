<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Clamav;

use MinVWS\DUSi\Shared\Application\Models\Submission\File;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;

class ClamavValidator extends \Sunspikes\ClamavValidator\ClamavValidator
{
    public function validateClamav(string $attribute, $value, array $parameters): bool
    {
        if (!($value instanceof FileList)) {
            return parent::validateClamav($attribute, $value, $parameters);
        }

        return parent::validateClamav($attribute, $value->items, $parameters);
    }

    protected function getFilePath($file): string
    {
        if (!($file instanceof File)) {
            return parent::getFilePath($file);
        }

        // TODO: Get file path from repository
        return '';
    }
}
