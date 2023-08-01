<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SubsidyStageData;
use Illuminate\Http\JsonResponse;

class SubsidyStageController extends Controller
{
    public function show(SubsidyStageData $subsidyStageData): JsonResponse
    {
        return JsonResponse::fromJsonString($subsidyStageData->json);
    }
}
