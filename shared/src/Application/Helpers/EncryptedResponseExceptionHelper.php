<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Helpers;

use Illuminate\Contracts\Translation\Translator;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

class EncryptedResponseExceptionHelper
{
    public function __construct(
        private ResponseEncryptionService $encryptionService,
        private LoggerInterface $logger,
        private Translator $translator
    ) {
    }

    public function processException(
        Throwable $exceptionToProcess,
        string $originClass,
        string $originMethod,
        string $translationNamespace,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $exception = EncryptedResponseException::forThrowable($exceptionToProcess);
        $error = $this->toError($exception, $translationNamespace);

        if ($exception->logAsError()) {
            $this->logException($exception, $originClass, $originMethod, $error);
        }

        return $this->encryptionService->encryptCodable(
            $exception->getStatus(),
            $error,
            $publicKey
        );
    }

    private function getTranslation(string $key): ?string
    {
        $translation = $this->translator->get($key, locale: 'nl');
        return is_string($translation) && $translation !== $key ? $translation : null;
    }

    private function toError(EncryptedResponseException $exception, string $translationNamespace): Error
    {
        $baseKey = $translationNamespace . '.' . $exception->getErrorCode();
        $code = $this->getTranslation($baseKey .  '_code');
        $message = $this->getTranslation($baseKey . '_message');

        if ($code === null || $message === null) {
            $code = strtolower($exception->getStatus()->name);
            $message = $this->getTranslation('generalResponseErrors.error_' . $code) ?? $code;
        }

        return new Error($code, $message);
    }

    private function logException(
        EncryptedResponseException $exception,
        string $originClass,
        string $originMethod,
        Error $error
    ): void {
        $this->logger->log(
            $this->isUnexpectedException($exception) ? LogLevel::ERROR : LogLevel::INFO,
            sprintf(
                'Error %s / %s in %s::%s: %s (%s)',
                $exception->getStatus()->name,
                $exception->getErrorCode(),
                $originClass,
                $originMethod,
                $error->message,
                $error->code
            ),
            ['trace' => $exception->getTraceAsString()]
        );

        if ($this->isUnexpectedException($exception) && $exception->getPrevious() !== null) {
            $this->logger->error(
                sprintf(
                    'Previous error: %s',
                    $exception->getPrevious()->getMessage()
                ),
                ['trace' => $exception->getPrevious()->getTraceAsString()]
            );
        }
    }

    private function isUnexpectedException(EncryptedResponseException $e): bool
    {
        return $e->getStatus() === EncryptedResponseStatus::INTERNAL_SERVER_ERROR;
    }
}
