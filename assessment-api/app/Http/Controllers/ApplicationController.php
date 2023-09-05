<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Http\Request;
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
        return $this->applicationService->getApplications(ApplicationsFilter::fromArray($request->validated()));
    }

    /**
     * Display the specified resource.
     * @throws \Exception
     */
    public function show(Request $request, Application $application): ApplicationSubsidyVersionResource
    {
        try {
            $publicKey = $request->hasHeader('publicKey') ? $request->header('publicKey')
                : throw new \Exception('No public key provided');
            $publicKey = $publicKey ?? throw new \Exception('No public key provided');
            return $this->applicationSubsidyService->getApplicationSubsidyResource(
                $application,
                $publicKey
            );
        } catch (\Exception $e) {
            if (!\Config::get('encryption.backend_encrypt')) { // TODO: remove when frontend supports decryption
                return $this->applicationSubsidyService->getApplicationSubsidyResource($application);
            }
            throw $e;
        }
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
}
