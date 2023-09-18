<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Traits;

use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use Throwable;

trait HandleException
{
    private function handleException(
        string $method,
        Throwable $exception,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $wrappedException = EncryptedResponseException::forThrowable($exception);

        $this->logger->error(
            sprintf(
                'Error %s in %s::%s: %s',
                $wrappedException->getStatus()->name,
                get_class($this),
                $method,
                $exception->getMessage()
            ),
            ['trace' => $exception->getTraceAsString()]
        );

        return $this->responseEncryptionService->encryptCodable(
            $wrappedException->getStatus(),
            $wrappedException->getError(),
            $publicKey
        );
    }
}
