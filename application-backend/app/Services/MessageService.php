<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Application\Repositories\LetterRepository;
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
        private readonly IdentityService $identityService,
        private readonly LetterRepository $letterRepository,
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

        if (!empty($identity)) {
            $applicationMessage = $this->messageRepository->getMyMessage($identity, $params->id);
        }

        if (!empty($applicationMessage)) {
            $htmlContent = $this->letterRepository->getHtmlContent($applicationMessage);
        }

        if (empty($identity) || empty($applicationMessage) || empty($htmlContent)) {
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
            $htmlContent,
        );

        return $this->encryptionService->encryptResponse(EncryptedResponseStatus::OK, $message, $params->publicKey);
    }

    public function getMessageDownload(MessageDownloadParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);

        if (!empty($identity)) {
            $applicationMessage = $this->messageRepository->getMyMessage($identity, $params->id);
        }

        if (!empty($applicationMessage)) {
            $pdfContent = $this->letterRepository->getPdfContent($applicationMessage);
        }

        if (empty($identity) || empty($applicationMessage) || empty($pdfContent)) {
            return $this->encryptionService->encryptResponse(
                EncryptedResponseStatus::NOT_FOUND,
                null,
                $params->publicKey
            );
        }

        $download = new MessageDownload('application/pdf', $pdfContent);

        return $this->encryptionService->encryptResponse(EncryptedResponseStatus::OK, $download, $params->publicKey);
    }
}
