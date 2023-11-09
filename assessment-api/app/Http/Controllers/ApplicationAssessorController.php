<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use MinVWS\DUSi\Assessment\API\Events\Logging\ClaimAssessmentEvent;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Services\ApplicationAssessorService;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\AlreadyAssignedException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidAssignmentException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidReleaseException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationAssessorController extends Controller
{
    public function __construct(
        private ApplicationAssessorService $assessorService,
        private ApplicationSubsidyService $applicationSubsidyService,
        private readonly LogService $logger,
    ) {
    }

    public function claim(Application $application, Authenticatable $user): ApplicationSubsidyVersionResource
    {
        $this->authorize('claim', $application);
        try {
            assert($user instanceof User);
            $this->assessorService->assignApplication($application, $user);

            $this->logger->log(
                (new ClaimAssessmentEvent())
                    ->withActor($user)
                    ->withData(
                        [
                            'applicationId' => $application->id,
                            'userId' => $user->getAuthIdentifier(),
                            'type' => 'application',
                            'typeId' => 1,
                        ]
                    )
            );

            return $this->applicationSubsidyService->getApplicationSubsidyResource($application, false);
        } catch (InvalidAssignmentException) {
            abort(Response::HTTP_BAD_REQUEST);
        } catch (AlreadyAssignedException) {
            abort(Response::HTTP_FORBIDDEN);
        }
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
