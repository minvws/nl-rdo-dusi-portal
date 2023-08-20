<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use MinVWS\DUSi\Application\API\Models\SubsidyStageData;
use Illuminate\Http\JsonResponse;

class SubsidyStageController extends Controller
{
    public function show(SubsidyStageData $subsidyStageData): JsonResponse
    {
        return JsonResponse::fromJsonString($subsidyStageData->json);
    }
}
