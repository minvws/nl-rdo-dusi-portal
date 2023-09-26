<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Services\ApplicationAssignmentService;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use MinVWS\DUSi\Shared\Application\Models\Application;
use Symfony\Component\HttpFoundation\Response;

class ApplicationAssignmentController extends Controller
{
    public function __construct(
        private ApplicationAssignmentService $assignmentService,
        private ApplicationSubsidyService $applicationSubsidyService
    ) {
    }

    public function claim(Application $application, User $user): ApplicationSubsidyVersionResource
    {
        $this->assignmentService->assignApplication($application, $user);
        return $this->applicationSubsidyService->getApplicationSubsidyResource($application);
    }

    public function release(Application $application, User $user): Response
    {
        $this->assignmentService->releaseApplication($application, $user);
        return response('', Response::HTTP_NO_CONTENT);
    }
}
