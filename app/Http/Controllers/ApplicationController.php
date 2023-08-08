<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationFilterResource;
use App\Http\Resources\ApplicationResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return ApplicationResource::collection(Application::all());
    }

    /**
     * Display a listing of applications with filters on specific fields.
     */
    public function getFilteredApplications(ApplicationRequest $request): AnonymousResourceCollection
    {
        $validatedData = $request->validated();

        $query = Application::query();

        $query->when(isset($validatedData['application_title']), function () use ($validatedData, $query) {
            $query->title($validatedData['application_title']);
        });

        $query->when(isset($validatedData['date_from']), function () use ($validatedData, $query) {
            $query->createdAtFrom(Carbon::parse($validatedData['date_from']))->get();
        });

        $query->when(isset($validatedData['date_to']), function () use ($validatedData, $query) {
            $query->createdAtTo(Carbon::parse($validatedData['date_to']))->get();
        });

        $query->when(isset($validatedData['date_last_modified_from']), function () use ($validatedData, $query) {
            $query->updatedAtFrom(Carbon::parse($validatedData['date_last_modified_from']))->get();
        });

        $query->when(isset($validatedData['date_last_modified_to']), function () use ($validatedData, $query) {
            $query->updatedAtTo(Carbon::parse($validatedData['date_last_modified_to']))->get();
        });

        $query->when(
            isset($validatedData['date_final_review_deadline_from']),
            function () use ($validatedData, $query) {
                $query->finalReviewDeadlineFrom(
                    Carbon::parse($validatedData['date_final_review_deadline_from'])
                )->get();
            }
        );

        $query->when(isset($validatedData['date_final_review_deadline_to']), function () use ($validatedData, $query) {
            $query->finalReviewDeadlineTo(Carbon::parse($validatedData['date_final_review_deadline_to']))->get();
        });

        $query->when(isset($validatedData['status']), function () use ($validatedData, $query) {
            $query->status(ApplicationStageVersionStatus::from($validatedData['status']))->get();
        });

        $query->when(isset($validatedData['subsidy']), function () use ($validatedData, $query) {
            $query->subsidyTitle($validatedData['subsidy'])->get();
        });

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
