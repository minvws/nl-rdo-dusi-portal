<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Message;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownload;
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
        private readonly IdentityService $identityService
    ) {
    }

    public function listMessages(MessageListParams $params): MessageList
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if ($identity === null) {
            return new MessageList([]);
        }

        $applicationMessages = $this->messageRepository->getMyApplicationMessages($identity);

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
        if ($identity !== null) {
            $applicationMessage = $this->messageRepository->getMyApplicationMessage($identity, $params->id);
        }

        if ($identity === null || $applicationMessage === null) {
            return $this->encryptionService->encryptResponse(
                EncryptedResponseStatus::NOT_FOUND,
                null,
                $params->publicKey
            );
        }

        $message = new Message(
            $applicationMessage->id,
            $applicationMessage->subject,
            $applicationMessage->sent_at,
            $applicationMessage->is_new,
            'Dit is een mock bericht.'
        );

        return $this->encryptionService->encryptResponse(EncryptedResponseStatus::OK, $message, $params->publicKey);
    }

    public function getMessageDownload(MessageDownloadParams $params): EncryptedResponse
    {
        $download = new MessageDownload('application/pdf', 'PDF mock');

        return $this->encryptionService->encryptResponse(EncryptedResponseStatus::OK, $download, $params->publicKey);
    }
}
