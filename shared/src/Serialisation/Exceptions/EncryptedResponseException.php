<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Exceptions;

use Exception;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use Throwable;

class EncryptedResponseException extends Exception
{
    public function __construct(
        private readonly EncryptedResponseStatus $status,
        private readonly string $errorCode,
        private readonly string $errorMessage,
        ?Throwable $previous = null
    ) {
        parent::__construct($errorMessage, previous: $previous);
    }

    public function getStatus(): EncryptedResponseStatus
    {
        return $this->status;
    }

    public function getError(): Error
    {
        return new Error($this->errorCode, $this->errorMessage);
    }

    public static function forThrowable(Throwable $e): EncryptedResponseException
    {
        if ($e instanceof self) {
            return $e;
        }

        return new self(
            EncryptedResponseStatus::INTERNAL_SERVER_ERROR,
            'internal_error',
            'Internal error.',
            previous: $e
        );
    }
}
