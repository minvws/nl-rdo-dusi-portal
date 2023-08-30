<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use DateTimeImmutable;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListMessage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use Ramsey\Uuid\Uuid;

class MessageService
{
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
}
