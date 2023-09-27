<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Exceptions;

use Exception;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use Throwable;

class EncryptedResponseException extends Exception
{
    public function __construct(
        private readonly EncryptedResponseStatus $status,
        private readonly string $errorCode,
        ?Throwable $previous = null
    ) {
        parent::__construct($status->value . ': ' . $this->errorCode, previous: $previous);
    }

    public function getStatus(): EncryptedResponseStatus
    {
        return $this->status;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public static function forThrowable(Throwable $e): EncryptedResponseException
    {
        if ($e instanceof self) {
            return $e;
        }

        return new self(
            EncryptedResponseStatus::INTERNAL_SERVER_ERROR,
            'internal_error',
            previous: $e
        );
    }
}
