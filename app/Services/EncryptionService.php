<?php
declare(strict_types=1);

namespace App\Services;

use App\Shared\Models\Definition\Submission\FieldValue;

class EncryptionService
{
    public function decryptFormSubmit(string $encryptedData): string
    {
        // TODO: currently not encrypted yet
        return $encryptedData;
    }

    public function decryptFileUpload(string $encryptedData): string
    {
        // TODO: currently not encrypted yet
        return $encryptedData;
    }

    public function encryptFieldValue(string $json): string
    {
        // TODO: encrypt
        return $json;
    }

}
