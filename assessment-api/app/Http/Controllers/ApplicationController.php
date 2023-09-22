<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Assessment\API\Http\Requests\ApplicationRequest;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationCountResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationMessageFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationRequestsFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Services\ApplicationService;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationSubsidyService $applicationSubsidyService,
        private ApplicationService $applicationService,
        private ApplicationDataService $applicationDataService,
        private ApplicationFlowService $applicationFlowService,
    ) {
    }


    /**
     * Display a listing of applications with filters on specific fields.
     * @throws \Exception
     */
    public function filterApplications(ApplicationRequest $request): AnonymousResourceCollection
    {
        return $this->applicationService->getApplications(ApplicationsFilter::fromArray($request->validated()));
    }

    /**
     * Display the specified resource.
     * @throws \Exception
     */
    public function show(Application $application): ApplicationSubsidyVersionResource
    {
        return $this->applicationSubsidyService->getApplicationSubsidyResource($application);
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

    public function submitAssessment(Application $application, Request $request): ApplicationSubsidyVersionResource
    {
        //Validations:
        // - isReviewableForAssessor
        // - field validations (not mvp)

        /** @var ApplicationSaveBody $submittedData */
        $submittedData = $request->json();

        $applicationStage = $application->currentApplicationStage;

        //Save data
        $this->applicationDataService->saveApplicationStageData($applicationStage, $submittedData->data);

        //Stage flow
        if ($submittedData->submit) {
            $this->applicationFlowService->submitApplicationStage($applicationStage);
        }

        $application->refresh();

        return $this->applicationSubsidyService->getApplicationSubsidyResource($application);
    }
}
