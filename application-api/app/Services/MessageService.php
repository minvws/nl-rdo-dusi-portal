<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Http\Resources\MessageFiltersResource;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedPayload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class MessageService
{
    public function __construct(
        private readonly Client $bridgeClient,
        private readonly SubsidyRepository $subsidyRepository
    ) {
    }

    public function listMessages(MessageListParams $params): MessageList
    {
        return $this->bridgeClient->call(RPCMethods::LIST_MESSAGES, $params, MessageList::class);
    }

    public function getFilters(): MessageFiltersResource
    {
        $shortRegulations = $this->subsidyRepository->getActiveSubsidyCodes();
        return MessageFiltersResource::make(['shortRegulations' => $shortRegulations]);
    }
    public function getMessage(MessageParams $params): EncryptedPayload
    {
        return $this->bridgeClient->call(RPCMethods::GET_MESSAGE, $params, EncryptedPayload::class);
    }

    public function getMessageDownload(MessageDownloadParams $params): EncryptedPayload
    {
        return $this->bridgeClient->call(RPCMethods::GET_MESSAGE_DOWNLOAD, $params, EncryptedPayload::class);
    }
}
