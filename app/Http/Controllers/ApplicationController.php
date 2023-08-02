<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use DB;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Ramsey\Uuid\Uuid;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Retrieve all applications and return them as JSON
        $results = Application::all();
        return response()->json($results);
    }

    /**
     * Display a listing of applications with filters on specific fields.
     */
    public function getFilteredApplications(Request $request): JsonResponse
    {
        $query = Application::query();
        if ($request->has('final_review_deadline')) {
            $query->where('final_review_deadline', '>=' , $request->final_review_deadline);
        }

        if ($request->has('status')) {
            $query->whereHas('applicationStages', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->has('user_id')) {
            if(Uuid::isValid($request->user_id) === false)
            {
                return response()->json(['error' => 'Invalid user UUID'], 400);
            }
            $query->whereHas('applicationStages', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        if ($request->has('subsidy_title')) {
            $subVersions = SubsidyVersion::query()->whereHas('subsidy', function ($q) use ($request) {
                $q->where('title', (string)$request->subsidy_title);
            })->pluck('id');

            $query->whereIn('subsidy_version_id', $subVersions);
        }

        if ($request->has('application_title')) {
            $query->where('application_title', (string)$request->application_title);
        }

        // Get the final results after applying filters
        $data = $query->get();

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application): JsonResponse
    {
        return response()->json($application);
    }
}
