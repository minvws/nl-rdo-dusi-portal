<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Exception;
use MinVWS\DUSi\Application\API\Services\ActionableService;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Shared\Serialisation\Http\Responses\EncodableResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCountsParams;

class ActionableController extends Controller
{
    /**
     * @throws Exception
     */
    public function counts(
        StateService $stateService,
        ActionableService $actionableService
    ): EncodableResponse {
        // TODO: implement
        $params = new ActionableCountsParams($stateService->getEncryptedIdentity());
        $counts = $actionableService->getActionableCounts($params);
        return new EncodableResponse($counts);
    }
}
