<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Services\ActionableService;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCountsParams;

class ActionableController extends Controller
{
    /**
     * @throws Exception
     */
    public function counts(
        StateService $stateService,
        ActionableService $actionableService,
        ClientPublicKeyHelper $publicKeyHelper,
    ): Response {
        $params = new ActionableCountsParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey()
        );

        $response = $actionableService->getActionableCounts($params);

        return $this->encryptedResponse($response);
    }
}
