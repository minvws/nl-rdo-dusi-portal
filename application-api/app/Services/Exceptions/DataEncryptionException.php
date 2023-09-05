<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services\Exceptions;

use Exception;

class DataEncryptionException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct('Encryption failed!, ' . $message);
    }
}
