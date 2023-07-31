<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use DB;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

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
            $query->whereHas('applicationStages', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        if ($request->has('subsidy_title')) {
            SubsidyVersion::query()->whereHas('subsidy', function ($q) use ($request) {
                $q->where('title', $request->subsidy_title);
            });
            SubsidyVersion::query()->whereHas('subsidy', function ($q) use ($request) {
                $q->where('title', $request->subsidy_title);
            });
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
        // Return the specific application as JSON
        return response()->json($application);
    }
}
