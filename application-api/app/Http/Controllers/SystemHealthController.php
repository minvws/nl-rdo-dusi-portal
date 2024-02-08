<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Illuminate\Http\JsonResponse;
use MinVWS\DUSi\Application\API\Services\SystemHealthService;

class SystemHealthController extends Controller
{
    public function __construct(private readonly SystemHealthService $systemHealthService)
    {
    }

    public function index(): JsonResponse
    {
        $json = $this->systemHealthService->getSystemHealthStatus();

        return response()->json($json);
    }
}
