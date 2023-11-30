<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use MinVWS\DUSi\Assessment\API\Http\Requests\ApplicationAssignRequest;
use MinVWS\DUSi\Assessment\API\Events\Logging\ClaimAssessmentEvent;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Services\ApplicationAssessorService;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\AlreadyAssignedException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidAssignmentException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidReleaseException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\User\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use MinVWS\Logging\Laravel\LogService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationAssessorController extends Controller
{
    public function __construct(
        private readonly ApplicationAssessorService $assessorService,
        private readonly ApplicationSubsidyService $applicationSubsidyService,
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

    /**
     * @throws AuthorizationException
     */
    public function getAssessorPool(Application $application, Request $request): AnonymousResourceCollection
    {
        $this->authorize('assign', $application);

        $search = $request->query('search');
        if ($search !== null && !is_string($search)) {
            abort(ResponseAlias::HTTP_BAD_REQUEST, 'Invalid search parameter');
        }

        return $this->assessorService->getAssessorPool($application, $search);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function assign(
        Application $application,
        ApplicationAssignRequest $request,
    ): ApplicationSubsidyVersionResource {
        $this->authorize('assign', $application);

        try {
            $this->assessorService->assignApplicationByUserId($application, $request->validated('id'));
            return $this->applicationSubsidyService->getApplicationSubsidyResource($application, false);
        } catch (InvalidAssignmentException) {
            abort(ResponseAlias::HTTP_BAD_REQUEST);
        } catch (AlreadyAssignedException) {
            abort(ResponseAlias::HTTP_FORBIDDEN);
        }
    }
}
