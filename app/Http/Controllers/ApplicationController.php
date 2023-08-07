<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationFilterResource;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Shared\Application\Models\Application;
use Illuminate\Http\JsonResponse;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class ApplicationController extends Controller
{
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

        if (isset($validatedData['application_title'])) {
            $query->where('application_title', (string)$validatedData['application_title']);
        }

        if (isset($validatedData['date_from'])) {
            $query->where('created_at', '>=', $validatedData['date_from']);
        }

        if (isset($validatedData['date_to'])) {
            $query->where('created_at', '<=', $validatedData['date_to']);
        }

        if (isset($validatedData['date_last_modified_from'])) {
            $query->where('updated_at', '>=', $validatedData['date_last_modified_from']);
        }

        if (isset($validatedData['date_last_modified_to'])) {
            $query->where('updated_at', '<=', $validatedData['date_last_modified_to']);
        }

        if (isset($validatedData['date_final_review_deadline_from'])) {
            $query->where('final_review_deadline', '>=', $validatedData['date_final_review_deadline_from']);
        }

        if (isset($validatedData['date_final_review_deadline_to'])) {
            $query->where('final_review_deadline', '<=', $validatedData['date_final_review_deadline_to']);
        }

        if (isset($validatedData['status'])) {
            $query->whereHas('applicationStages.applicationStageVersions', function ($q) use ($validatedData) {
                $q->where('status', $validatedData['status']);
            });
        }

        if (isset($validatedData['subsidy'])) {
            $subVersions = SubsidyVersion::query()->whereHas('subsidy', function ($q) use ($validatedData) {
                $q->where('title', (string)$validatedData['subsidy']);
            })->pluck('id');

            $query->whereIn('subsidy_version_id', $subVersions);
        }

        // Get the final results after applying filters
        $applications = $query->get();
        return ApplicationFilterResource::Collection($applications);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application): ApplicationResource
    {
        return new ApplicationResource($application);
    }
}
