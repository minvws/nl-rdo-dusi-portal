<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use DateTimeImmutable;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Message;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListMessage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;
use Ramsey\Uuid\Uuid;

class MessageService
{
    public function __construct(private readonly EncryptionService $encryptionService)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function listMessages(MessageListParams $params): MessageList
    {
        // TODO: fill message list based on the available messages in `application_stage`
        return new MessageList([
            new MessageListMessage(
                Uuid::uuid4()->toString(),
                'Aanvraag ontvangen "Borstprothesen transvrouwen"',
                new DateTimeImmutable(),
                true
            )
        ]);
    }

    public function getMessage(MessageParams $params): EncryptedResponse
    {
        $message = new Message(
            Uuid::uuid4()->toString(),
            'Aanvraag ontvangen "Borstprothesen transvrouwen"',
            new DateTimeImmutable(),
            true,
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
