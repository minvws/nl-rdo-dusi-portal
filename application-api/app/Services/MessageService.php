<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;

class MessageService
{
    public function __construct(private readonly Client $bridgeClient)
    {
    }

    public function listMessages(MessageListParams $params): MessageList
    {
        return $this->bridgeClient->call(RPCMethods::LIST_MESSAGES, $params, MessageList::class);
    }
}
