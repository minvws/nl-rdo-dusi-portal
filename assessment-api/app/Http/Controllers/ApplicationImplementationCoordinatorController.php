<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use MinVWS\DUSi\Assessment\API\Services\ApplicationAssessorService;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidReleaseException;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationImplementationCoordinatorController extends Controller
{
    public function __construct(
        private ApplicationAssessorService $assessorService,
    ) {
    }

    public function release(Application $application): Response|ResponseFactory
    {
        $this->authorize('release', $application);
        try {
            $this->assessorService->releaseApplication($application);
            return response('', Response::HTTP_NO_CONTENT);
        } catch (InvalidReleaseException) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
