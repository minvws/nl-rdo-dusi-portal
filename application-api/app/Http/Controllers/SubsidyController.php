<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use MinVWS\DUSi\Application\API\Exceptions\SubsidyNotFoundException;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Application\API\Services\SubsidyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use MinVWS\DUSi\Application\API\Services\SubsidyStageService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConceptsParams;

class SubsidyController extends Controller
{
    public function __construct(
        private readonly SubsidyService $subsidyService,
        private readonly SubsidyStageService $subsidyStageService,
    ) {
    }

    public function index(): JsonResponse
    {
        $json = $this->subsidyService->getActiveSubsidies();
        return JsonResponse::fromJsonString($json);
    }

    public function getSubsidyAndConcepts(
        string $subsidyCode,
        ClientPublicKeyHelper $publicKeyHelper,
        StateService $stateService,
    ): Response {
        $params = new SubsidyConceptsParams(
            $stateService->getEncryptedIdentity(),
            $publicKeyHelper->getClientPublicKey(),
            $subsidyCode
        );
        $response = $this->subsidyService->getSubsidyConcepts($params);
        return $this->encryptedResponse($response);
    }

    public function getSubsidyForm(string $subsidyCode): JsonResponse
    {
        try {
            $subsidy = $this->subsidyService->getSubsidyByCode($subsidyCode);
        } catch (SubsidyNotFoundException $e) {
            return response()->json(['message' => 'Subsidy not Found!'], 404);
        }

        $subsidyStageId = $this->subsidyService->getCurrentApplicantStageForSubsidy($subsidy);

        $subsidyStageData = $this->subsidyStageService->getSubsidyStageData($subsidyStageId);
        return JsonResponse::fromJsonString($subsidyStageData->json);
    }
}
