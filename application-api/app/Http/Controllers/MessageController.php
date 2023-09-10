<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Http\Requests\MessageRequest;
use MinVWS\DUSi\Application\API\Http\Resources\MessageFiltersResource;
use MinVWS\DUSi\Application\API\Services\MessageService;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Shared\Serialisation\Http\Responses\EncodableResponse;
use MinVWS\DUSi\Shared\Serialisation\Http\Responses\EncodableResponseBuilder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;

class MessageController extends Controller
{
    public function __construct(
        private readonly StateService $stateService,
        private readonly MessageService $messageService
    ) {
    }

    public function index(MessageRequest $request): EncodableResponse
    {
        $params = new MessageListParams(
            $this->stateService->getEncryptedIdentity(),
            $request->validated('date_from'),
            $request->validated('date_to'),
            $request->validated('subsidies'),
            $request->validated('statuses'),
        );

        $list = $this->messageService->listMessages($params);

        return new EncodableResponse($list);
    }

    public function showFilters(): MessageFiltersResource
    {
        return $this->messageService->getFilters();
    }

    public function view(string $id, ClientPublicKeyHelper $publicKeyHelper): EncodableResponse
    {
        $params = new MessageParams(
            $this->stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $id
        );
        $response = $this->messageService->getMessage($params);
        return EncodableResponseBuilder::create($response, $response->status->value)->build();
    }

    public function download(
        string $id,
        string $format,
        ClientPublicKeyHelper $publicKeyHelper
    ): EncodableResponse {
        $params = new MessageDownloadParams(
            $this->stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $id,
            MessageDownloadFormat::from($format)
        );
        $response = $this->messageService->getMessageDownload($params);
        return EncodableResponseBuilder::create($response, $response->status->value)->build();
    }
}
