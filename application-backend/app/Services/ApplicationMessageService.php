<?php

/**
 * Application Message Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Application\Repositories\LetterRepository;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationMessageService
{
    use LoadIdentity;

    public function __construct(
        private ResponseEncryptionService $responseEncryptionService,
        private ApplicationMessageRepository $messageRepository,
        private IdentityService $identityService,
        private LetterRepository $letterRepository,
        private ApplicationMapper $applicationMapper,
        private EncryptedResponseExceptionHelper $exceptionHelper
    ) {
    }

    public function listMessages(MessageListParams $params): EncryptedResponse
    {
        try {
            return $this->doListMessages($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::LIST_MESSAGES,
                $params->publicKey
            );
        }
    }

    private function doListMessages(MessageListParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if ($identity === null) {
            // Identity not known in system, so no applications / messages yet.
            return $this->responseEncryptionService->encryptCodable(
                EncryptedResponseStatus::OK,
                new MessageList([]),
                $params->publicKey
            );
        }

        $applicationMessages = $this->messageRepository->getMyMessages($identity);
        $list = $this->applicationMapper->mapApplicationMessageArrayToMessageListDTO($applicationMessages);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $list,
            $params->publicKey
        );
    }

    public function getMessage(MessageParams $params): EncryptedResponse
    {
        try {
            return $this->doGetMessage($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_MESSAGE,
                $params->publicKey
            );
        }
    }

    private function doGetMessage(MessageParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);

        $message = $this->messageRepository->getMyMessage($identity, $params->id);
        if ($message === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_not_found'
            );
        }

        $body = $this->letterRepository->getHtmlContent($message);
        // TODO: should be encrypted $body = $this->encryptionService->decryptData($body);

        if ($body === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_file_not_found'
            );
        }

        $dto = $this->applicationMapper->mapApplicationMessageToMessageDTO($message, $body);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $dto,
            $params->publicKey
        );
    }

    public function getMessageDownload(MessageDownloadParams $params): EncryptedResponse
    {
        try {
            return $this->doGetMessageDownload($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_MESSAGE_DOWNLOAD,
                $params->publicKey
            );
        }
    }

    private function doGetMessageDownload(MessageDownloadParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);

        $message = $this->messageRepository->getMyMessage($identity, $params->id);
        if ($message === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_not_found'
            );
        }

        $content = match ($params->format) {
            MessageDownloadFormat::HTML => $this->letterRepository->getHtmlContent($message),
            MessageDownloadFormat::PDF => $this->letterRepository->getPdfContent($message),
        };

        $contentType =  match ($params->format) {
            MessageDownloadFormat::HTML => 'text/html',
            MessageDownloadFormat::PDF => 'application/pdf'
        };

        if ($content === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_file_not_found'
            );
        }

        return $this->responseEncryptionService->encrypt(
            EncryptedResponseStatus::OK,
            $content,
            $contentType,
            $params->publicKey
        );
    }
}
