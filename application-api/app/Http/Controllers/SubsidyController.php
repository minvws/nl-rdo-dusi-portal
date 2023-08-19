<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SubsidyService;
use Illuminate\Http\JsonResponse;

class SubsidyController extends Controller
{
    public function index(SubsidyService $subsidyService): JsonResponse
    {
        $json = $subsidyService->getActiveSubsidies();
        return JsonResponse::fromJsonString($json);
    }
}
