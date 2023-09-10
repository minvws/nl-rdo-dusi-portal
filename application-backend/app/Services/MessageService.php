<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Application\Repositories\LetterRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListMessage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MessageService
{
    public function __construct(
        private readonly EncryptionService $encryptionService,
        private readonly ApplicationMessageRepository $messageRepository,
        private readonly IdentityService $identityService,
        private readonly LetterRepository $letterRepository,
        private readonly ApplicationMapper $applicationMapper
    ) {
    }

    public function listMessages(MessageListParams $params): MessageList
    {
        $identity = $this->identityService->findIdentity($params->identity);

        if (empty($identity)) {
            return new MessageList([]);
        }

        $applicationMessages = $this->messageRepository->getMyMessages($identity);

        $messageListMessages = array_map(fn(ApplicationMessage $message) => new MessageListMessage(
            $message->id,
            $message->subject,
            $message->sent_at,
            $message->is_new,
        ), $applicationMessages);

        return new MessageList($messageListMessages);
    }

    public function getMessage(MessageParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if ($identity === null) {
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('identity_not_found', 'Identity not registered yet.'),
                $params->publicKey
            );
        }

        $message = $this->messageRepository->getMyMessage($identity, $params->id);
        if ($message === null) {
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('message_not_found', 'Message not found.'),
                $params->publicKey
            );
        }

        $body = $this->letterRepository->getHtmlContent($message);
        if ($body === null) {
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('message_body_not_found', 'Message body not found.'),
                $params->publicKey
            );
        }

        $dto = $this->applicationMapper->mapApplicationMessageToMessageDTO($message, $body);
        return $this->encryptionService->encryptCodableResponse(
            EncryptedResponseStatus::OK,
            $dto,
            $params->publicKey
        );
    }

    public function getMessageDownload(MessageDownloadParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if ($identity === null) {
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('identity_not_found', 'Identity not registered yet.'),
                $params->publicKey
            );
        }

        $message = $this->messageRepository->getMyMessage($identity, $params->id);
        if ($message === null) {
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('message_not_found', 'Message not found.'),
                $params->publicKey
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
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::NOT_FOUND,
                new Error('message_download_not_found', 'Message download not found.'),
                $params->publicKey
            );
        }

        return $this->encryptionService->encryptResponse(
            EncryptedResponseStatus::OK,
            $content,
            $contentType,
            $params->publicKey
        );
    }
}
