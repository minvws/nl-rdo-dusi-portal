<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use MinVWS\DUSi\Application\API\Http\Requests\MessageRequest;
use MinVWS\DUSi\Application\API\Http\Resources\MessageFiltersResource;
use MinVWS\DUSi\Application\API\Services\MessageService;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Shared\Serialisation\Http\Responses\EncodableResponse;
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
            $this->stateService->getIdentity(),
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
}
