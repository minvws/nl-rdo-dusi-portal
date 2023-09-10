<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Exception;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Http\Requests\ApplicationSubmitRequest;
use MinVWS\DUSi\Application\API\Http\Requests\ApplicationUploadFileRequest;
use MinVWS\DUSi\Application\API\Models\Application;
use MinVWS\DUSi\Application\API\Models\SubsidyStageData;
use MinVWS\DUSi\Application\API\Services\ApplicationService;
use MinVWS\DUSi\Application\API\Services\Exceptions\SubsidyStageNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\ResponseFactory;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationController extends Controller
{
    public function createDraft(
        SubsidyStageData $subsidyStageData,
        ApplicationService $applicationService
    ): JsonResponse {
        try {
            $id = $applicationService->createDraft($subsidyStageData);
            return response()->json(['id' => $id], status: 202);
        } catch (SubsidyStageNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function uploadFile(
        Application $application,
        ApplicationUploadFileRequest $request,
        ApplicationService $applicationService
    ): JsonResponse {
        $fieldCode = $request->safe()['fieldCode'];
        assert(is_string($fieldCode));
        $file = $request->safe()['file'];
        assert($file instanceof UploadedFile);
        $id = $applicationService->uploadFile($application, $fieldCode, $file);
        return response()->json(['id' => $id], status: 202);
    }

    public function submit(
        Application $application,
        ApplicationSubmitRequest $request,
        ApplicationService $applicationService
    ): Response|ResponseFactory {
        $encryptedData = $request->safe()['data'];
        assert(is_string($encryptedData));
        $applicationService->submit($application, $encryptedData);
        return response(status: 202);
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
        string $id,
        ClientPublicKeyHelper $publicKeyHelper,
        StateService $stateService,
        ApplicationService $applicationService
    ): Response {
        $params = new ApplicationParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $id,
            true
        );
        $response = $applicationService->getApplication($params);
        return $this->encryptedResponse($response);
    }
}
