<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ApplicationSubsidyVersionResource;
use App\Services\ApplicationService;
use App\Services\ApplicationSubsidyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationSubsidyService $applicationSubsidyService,
        private ApplicationService $applicationService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return ApplicationResource::collection(Application::all());
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
     */
    public function show(Application $application): ApplicationSubsidyVersionResource
    {
        return $this->applicationSubsidyService->getApplicationSubsidyResource($application);
    }
}
