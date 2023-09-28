<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Http\Response;
use MinVWS\DUSi\Assessment\API\Services\ApplicationFileService;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationFileController extends Controller
{
    public function __construct(
        private ApplicationFileService $applicationFileService
    ) {
    }
    public function show(
        Application $application,
        string $applicationStageId,
        string $fieldCode,
        string $fileId
    ): Response {
        $this->authorize('show', $application);
        return $this->applicationFileService->getApplicationFile(
            $application,
            $applicationStageId,
            $fieldCode,
            $fileId
        );
    }
}
