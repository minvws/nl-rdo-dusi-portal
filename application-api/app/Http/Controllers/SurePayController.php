<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Http\Requests\SurePayAccountCheckRequest;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Application\API\Services\SurePayService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SurePayAccountCheckParams;

class SurePayController extends Controller
{
    public function __construct(
        private readonly ClientPublicKeyHelper $publicKeyHelper,
        private readonly StateService $stateService,
        private readonly SurePayService $surePayService
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function accountCheck(SurePayAccountCheckRequest $request): Response
    {
        $params = new SurePayAccountCheckParams(
            $this->stateService->getEncryptedIdentity(),
            $this->publicKeyHelper->getClientPublicKey(),
            $request->input('bankAccountHolder'),
            $request->input('bankAccountNumber'),
        );
        $response = $this->surePayService->accountCheck($params);
        return $this->encryptedResponse($response);
    }
}
