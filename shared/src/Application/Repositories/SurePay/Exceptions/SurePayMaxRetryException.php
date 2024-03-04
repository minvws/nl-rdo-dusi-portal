<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions;

use Throwable;

class SurePayMaxRetryException extends SurePayRepositoryException
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null,
        public int $retries = 0
    ) {
        parent::__construct($message, $code, $previous);
    }
}
