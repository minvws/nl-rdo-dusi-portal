<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Application\API\Services\SubsidyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConceptsParams;

class SubsidyController extends Controller
{
    public function index(SubsidyService $subsidyService): JsonResponse
    {
        $json = $subsidyService->getActiveSubsidies();
        return JsonResponse::fromJsonString($json);
    }

    public function getSubsidyAndConcepts(
        string $subsidyCode,
        ClientPublicKeyHelper $publicKeyHelper,
        StateService $stateService,
        SubsidyService $subsidyService
    ): Response {
        $params = new SubsidyConceptsParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $subsidyCode
        );
        $response = $subsidyService->getSubsidyConcepts($params);
        return $this->encryptedResponse($response);
    }
}
