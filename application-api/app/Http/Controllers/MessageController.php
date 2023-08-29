<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\API\Services\MessageService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;

class MessageController extends Controller
{
    /**
     * @throws Exception
     */
    public function index(MessageService $messageService): JsonResponse
    {
        // TODO: implement
        $identity = new Identity(IdentityType::EncryptedCitizenServiceNumber, 'test');
        $params = new MessageListParams($identity, null, null, null);
        $list = $messageService->listMessages($params);
        $json = (new JSONEncoder())->encode($list);
        return JsonResponse::fromJsonString($json);
    }
}
