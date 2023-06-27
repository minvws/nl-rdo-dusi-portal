<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Submission\FieldValue;

class EncryptionService
{
    public function decryptFormSubmit(string $encryptedData): string
    {
        // TODO: currently not encrypted yet
        return $encryptedData;
    }

    public function encryptFieldValue(FieldValue $value): ?string
    {
        // TODO: encrypt
        return $value->value === null ? null : json_encode($value->value);
    }
}
