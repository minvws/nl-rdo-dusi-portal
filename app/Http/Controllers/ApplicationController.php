<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationFilterResource;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;

class ApplicationController extends Controller
{
    private ApplicationRepository $repository;

    public function __construct(ApplicationRepository $repository)
    {
        $this->repository = $repository;
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
        return ApplicationFilterResource::Collection($this->repository
            ->filterApplications(ApplicationsFilter::fromArray($request->validated())));
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application): ApplicationResource
    {
        return new ApplicationResource($application);
    }
}
