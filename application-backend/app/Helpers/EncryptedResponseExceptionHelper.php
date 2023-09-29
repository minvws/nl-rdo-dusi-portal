<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Helpers;

use Illuminate\Contracts\Translation\Translator;
use MinVWS\DUSi\Application\Backend\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class EncryptedResponseExceptionHelper
{
    public function __construct(
        private ResponseEncryptionService $encryptionService,
        private LoggerInterface $logger,
        private Translator $translator
    ) {
    }

    public function processException(
        Throwable $exception,
        string $originClass,
        string $originMethod,
        string $translationNamespace,
        ClientPublicKey $publicKey
    ): EncryptedResponse {
        $exception = EncryptedResponseException::forThrowable($exception);
        $error = $this->toError($exception, $translationNamespace);

        $this->logger->error(
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

        if ($exception->getPrevious() !== null) {
            $this->logger->error(
                sprintf('Previous error: %s', $exception->getPrevious()->getMessage()),
                ['trace' => $exception->getPrevious()->getTraceAsString()]
            );
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
}
