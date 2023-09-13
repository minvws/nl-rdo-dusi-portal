<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Exception;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Services\ApplicationFileService;
use Illuminate\Http\Response;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;

class ApplicationFileController extends Controller
{
    public function __construct(
        private readonly ClientPublicKeyHelper $publicKeyHelper,
        private readonly StateService $stateService,
        private readonly ApplicationFileService $applicationFileService
    ) {
    }

    /**
     * @throws Exception
     */
    public function show(
        string $applicationReference,
        string $fieldCode,
        string $id
    ): Response {
        $params = new ApplicationFileParams(
            $this->stateService->getEncryptedIdentity(),
            $this->publicKeyHelper->getClientPublicKey(),
            $applicationReference,
            $fieldCode,
            $id
        );
        $response = $this->applicationFileService->getApplicationFile($params);
        return $this->encryptedResponse($response);
    }

    /**
     * @throws Exception
     */
    public function delete(
        string $applicationReference,
        string $fieldCode,
        string $id
    ): Response {
        $params = new ApplicationFileParams(
            $this->stateService->getEncryptedIdentity(),
            $this->publicKeyHelper->getClientPublicKey(),
            $applicationReference,
            $fieldCode,
            $id
        );
        $response = $this->applicationFileService->deleteApplicationFile($params);
        return $this->encryptedResponse($response);
    }
}
