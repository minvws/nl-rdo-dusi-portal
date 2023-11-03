<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Assessment\API\Events\Logging\ViewApplicationEvent;
use MinVWS\DUSi\Assessment\API\Http\Requests\ApplicationRequest;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationCountResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationMessageFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationRequestsFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Services\ApplicationService;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidApplicationSaveException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidApplicationSubmitException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\TransitionNotFoundException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationSubsidyService $applicationSubsidyService,
        private ApplicationService $applicationService,
        private LogService $logger,
    ) {
    }


    /**
     * Display a listing of applications with filters on specific fields.
     * @throws \Exception
     */
    public function filterApplications(ApplicationRequest $request): AnonymousResourceCollection
    {
        $this->authorize('filterApplications', [Application::class]);

        $user = $request->user();
        assert($user !== null);

        $filter = ApplicationsFilter::fromArray($request->validated());
        return $this->applicationService->getApplications($user, false, $filter);
    }

    public function filterAssignedApplications(ApplicationRequest $request): AnonymousResourceCollection
    {
        $this->authorize('filterAssignedApplications', [Application::class]);

        $user = $request->user();
        assert($user !== null);

        $filter = ApplicationsFilter::fromArray($request->validated());

        return $this->applicationService->getApplications($user, true, $filter);
    }

    /**
     * Display the specified resource.
     * @throws \Exception
     */
    public function show(Application $application, Authenticatable $user): ApplicationSubsidyVersionResource
    {
        $this->authorize('show', $application);
        assert($user instanceof User);
        $this->logger->log((new ViewApplicationEvent())
            ->withData([
                'applicationId' => $application->id,
                'userId' => $user->id,
            ]));

        $readOnly =
            $application->currentApplicationStage === null ||
            $application->currentApplicationStage->assessor_user_id !== $user->id;

        return $this->applicationSubsidyService->getApplicationSubsidyResource($application, $readOnly);
    }

    /**
     * Get the count of applications for specific filters.
     */
    public function getApplicationsCount(): ApplicationCountResource
    {
        return $this->applicationService->getApplicationsCountMock();
    }

    /**
     * Get the filter UI resource.
     */
    public function getApplicationMessageFilterResource(): ApplicationMessageFilterResource
    {
        return $this->applicationService->getApplicationMessageFilterResource();
    }

    /**
     * Get the filter UI resource.
     */
    public function getApplicationRequestFilterForUserResource(): ApplicationRequestsFilterResource
    {
        //TODO: get and validate user from Auth
        return $this->applicationService->getApplicationRequestFilterResource(null);
    }

    /**
     * Get the filter UI resource.
     */
    public function getApplicationRequestFilterResource(): ApplicationRequestsFilterResource
    {
        return $this->applicationService->getApplicationRequestFilterResource(null);
    }

    public function getApplicationHistory(Application $application): ResourceCollection
    {
        return $this->applicationService->getApplicationStagesResource($application);
    }

    public function getApplicationReviewer(): JsonResource
    {
        //TODO: implement this
        return JsonResource::make([]);
    }

    public function getApplicationTransitions(Application $application): ResourceCollection
    {
        $this->authorize('getTransitionHistory', $application);

        return $this->applicationService->getApplicationStageTransitions($application);
    }

    public function getLetterForMessage(ApplicationMessage $message): Response
    {
        $this->authorize('getLetterFromMessage', $message->application);

        return $this->applicationService->getLetterFromMessage($message, MessageDownloadFormat::PDF);
    }

    public function saveAssessment(
        Application $application,
        Authenticatable $user,
        Request $request
    ): ApplicationSubsidyVersionResource {
        $this->authorize('save', $application);

        $submittedData = $request->json()->all();
        $data = (object)($submittedData['data'] ?? []);
        $submit = (bool)($submittedData['submit'] ?? false);

        assert($user instanceof User);

        try {
            $application = $this->applicationService->saveAssessment($application, $data, $submit);
            return $this->applicationSubsidyService->getApplicationSubsidyResource($application, true);
        } catch (InvalidApplicationSaveException) {
            abort(Response::HTTP_FORBIDDEN);
        } catch (ValidationException $exception) {
            Log::error('ValidationException while saveAssessment', [
                'userId' => $user->id,
                'applicationId' => $application->id,
                'submitAssessment' => $submit,
                'validationErrors' => $exception->errors(),
            ]);
            abort(Response::HTTP_BAD_REQUEST);
        }
    }

    public function previewTransition(
        Application $application,
        Authenticatable $user
    ): ApplicationSubsidyVersionResource {
        $this->authorize('previewTransition', $application);

        assert($user instanceof User);

        try {
            return $this->applicationSubsidyService->getApplicationTransitionPreview($application);
        } catch (TransitionNotFoundException) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }

    public function submitAssessment(Application $application): ApplicationSubsidyVersionResource
    {
        $this->authorize('submit', $application);

        try {
            $application = $this->applicationService->submitAssessment($application);
            return $this->applicationSubsidyService->getApplicationSubsidyResource($application, true);
        } catch (InvalidApplicationSubmitException) {
            abort(Response::HTTP_FORBIDDEN);
        } catch (ValidationException) {
            abort(Response::HTTP_BAD_REQUEST);
        }
    }
}
