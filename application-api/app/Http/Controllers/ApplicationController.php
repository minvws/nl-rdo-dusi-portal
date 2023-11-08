<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Services\ApplicationService;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFindOrCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationValidationParams;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationController extends Controller
{
    public function create(
        string $subsidyCode,
        StateService $stateService,
        ClientPublicKeyHelper $publicKeyHelper,
        ApplicationService $applicationService
    ): Response {
        $params = new ApplicationFindOrCreateParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $subsidyCode
        );
        $response = $applicationService->findOrCreateApplication($params);
        return $this->encryptedResponse($response);
    }

    public function uploadFile(
        string $applicationReference,
        string $fieldCode,
        Request $request,
        StateService $stateService,
        ClientPublicKeyHelper $publicKeyHelper,
        ApplicationService $applicationService
    ): Response {
        $params = new EncryptedApplicationFileUploadParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $applicationReference,
            $fieldCode,
            new BinaryData($request->getContent())
        );
        $response = $applicationService->uploadApplicationFile($params);
        return $this->encryptedResponse($response);
    }

    /**
     * @throws AuthenticationException
     * @throws Exception
     */
    public function save(
        string $reference,
        Request $request,
        StateService $stateService,
        ClientPublicKeyHelper $publicKeyHelper,
        ApplicationService $applicationService
    ): Response {
        $params = new EncryptedApplicationSaveParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $reference,
            new BinaryData($request->getContent())
        );
        $response = $applicationService->saveApplication($params);
        return $this->encryptedResponse($response);
    }

    /**
     * @throws AuthenticationException
     * @throws Exception
     */
    public function validateApplication(
        string $reference,
        Request $request,
        StateService $stateService,
        ClientPublicKeyHelper $publicKeyHelper,
        ApplicationService $applicationService
    ): Response {
        $params = new EncryptedApplicationValidationParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $reference,
            new BinaryData($request->getContent())
        );
        $response = $applicationService->validateApplicationFields($params);
        return $this->encryptedResponse($response);
    }

    /**
     * @throws Exception
     */
    public function index(
        StateService $stateService,
        ApplicationService $applicationService,
        ClientPublicKeyHelper $publicKeyHelper
    ): Response|ResponseFactory {
        $params = new ApplicationListParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey()
        );
        $response = $applicationService->listApplications($params);
        return $this->encryptedResponse($response);
    }

    /**
     * @throws Exception
     */
    public function show(
        string $reference,
        ClientPublicKeyHelper $publicKeyHelper,
        StateService $stateService,
        ApplicationService $applicationService
    ): Response {
        $params = new ApplicationParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $reference,
            true
        );
        $response = $applicationService->getApplication($params);
        return $this->encryptedResponse($response);
    }
}
