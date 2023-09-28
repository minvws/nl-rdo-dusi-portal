<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use MinVWS\DUSi\Assessment\API\Http\Requests\ApplicationRequest;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationCountResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationMessageFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationRequestsFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Services\ApplicationService;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidApplicationSaveException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationSubsidyService $applicationSubsidyService,
        private ApplicationService $applicationService
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
        return $this->applicationSubsidyService->getApplicationSubsidyResource($application, $user);
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

    public function getApplicationHistory(): JsonResource
    {
        //TODO: implement this
        return JsonResource::make([]);
    }

    public function getApplicationReviewer(): JsonResource
    {
        //TODO: implement this
        return JsonResource::make([]);
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
            return $this->applicationSubsidyService->getApplicationSubsidyResource($application, $user);
        } catch (InvalidApplicationSaveException) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
