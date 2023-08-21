<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use MinVWS\DUSi\Application\API\Services\SubsidyService;
use Illuminate\Http\JsonResponse;

class SubsidyController extends Controller
{
    public function index(SubsidyService $subsidyService): JsonResponse
    {
        $json = $subsidyService->getActiveSubsidies();
        return JsonResponse::fromJsonString($json);
    }
}
