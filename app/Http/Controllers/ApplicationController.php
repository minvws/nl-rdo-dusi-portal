<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationFilterResource;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ApplicationSubsidyVersionResource;
use App\Services\ApplicationSubsidyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class ApplicationController extends Controller
{

    public function __construct(private ApplicationSubsidyService $applicationSubsidyService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        // Retrieve all applications and return them as JSON
        return ApplicationResource::collection(Application::all());
    }

    /**
     * Display a listing of applications with filters on specific fields.
     */
    public function getFilteredApplications(ApplicationRequest $request): AnonymousResourceCollection
    {
        $validatedData = $request->validated();

        $query = Application::query();

        $query->when(isset($validatedData['application_title']), function ($q) use ($validatedData) {
            $q->where('application_title', (string)$validatedData['application_title']);
        });

        $query->when(isset($validatedData['date_from']), function ($q) use ($validatedData) {
            $q->where('created_at', '>=', $validatedData['date_from']);
        });

        $query->when(isset($validatedData['date_to']), function ($q) use ($validatedData) {
            $q->where('created_at', '<=', $validatedData['date_to']);
        });

        $query->when(isset($validatedData['date_last_modified_from']), function ($q) use ($validatedData) {
            $q->where('updated_at', '>=', $validatedData['date_last_modified_from']);
        });

        $query->when(isset($validatedData['date_last_modified_to']), function ($q) use ($validatedData) {
            $q->where('updated_at', '<=', $validatedData['date_last_modified_to']);
        });

        $query->when(isset($validatedData['date_final_review_deadline_from']), function ($q) use ($validatedData) {
            $q->where('final_review_deadline', '>=', $validatedData['date_final_review_deadline_from']);
        });

        $query->when(isset($validatedData['date_final_review_deadline_to']), function ($q) use ($validatedData) {
            $q->where('final_review_deadline', '<=', $validatedData['date_final_review_deadline_to']);
        });

        $query->when(isset($validatedData['status']), function ($q) use ($validatedData) {
            $q->whereHas('applicationStages.applicationStageVersions', function ($q) use ($validatedData) {
                $q->where('status', $validatedData['status']);
            });
        });

        $query->when(isset($validatedData['subsidy']), function ($q) use ($validatedData) {
            $subVersions = SubsidyVersion::query()->whereHas('subsidy', function ($q) use ($validatedData) {
                $q->where('title', (string)$validatedData['subsidy']);
            })->pluck('id');

            $q->whereIn('subsidy_version_id', $subVersions);
        });

        // Get the final results after applying filters
        $query->paginate(10);
        $applications = $query->get();
        return ApplicationFilterResource::Collection($applications);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application): ApplicationSubsidyVersionResource
    {
        return $this->applicationSubsidyService->getApplicationSubsidyResource($application);
    }
}
