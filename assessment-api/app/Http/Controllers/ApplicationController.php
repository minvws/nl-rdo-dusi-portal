<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Events\Logging\SubmitAssessmentEvent;
use MinVWS\DUSi\Assessment\API\Events\Logging\ViewApplicationEvent;
use MinVWS\DUSi\Assessment\API\Http\Requests\ApplicationRequest;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationCountResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationMessageFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationRequestsFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Services\ApplicationService;
use MinVWS\DUSi\Assessment\API\Services\ApplicationSubsidyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidApplicationSaveException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidApplicationSubmitException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\TransitionNotFoundException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\PaginationOptions;
use MinVWS\DUSi\Shared\Application\DTO\SortOptions;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ValidationErrorException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationSubsidyService $applicationSubsidyService,
        private ApplicationService $applicationService,
        private ApplicationRepository $applicationRepository,
        private LogService $logService,
    ) {
    }


    /**
     * Display a listing of applications with filters on specific fields.
     * @throws \Exception
     */
    public function filterApplications(ApplicationRequest $request): AnonymousResourceCollection
    {
        $this->authorize('filterApplications', [Application::class]);

        return $this->getFilteredApplications(request: $request, onlyMyApplications: false);
    }

    public function filterAssignedApplications(ApplicationRequest $request): AnonymousResourceCollection
    {
        $this->authorize('filterAssignedApplications', [Application::class]);

        return $this->getFilteredApplications(request: $request, onlyMyApplications: true);
    }

    /**
     * Display the specified resource.
     * @throws \Exception
     */
    public function show(Application $application, Authenticatable $user): ApplicationSubsidyVersionResource
    {
        $this->authorize('show', $application);
        assert($user instanceof User);
        $this->logService->log((new ViewApplicationEvent())
            ->withActor($user)
            ->withData([
                'applicationId' => $application->id,
                'userId' => $user->id,
            ]));

        $readOnly =
            $application->currentApplicationStage === null ||
            $application->currentApplicationStage->assessor_user_id !== $user->id;

        return $this->applicationSubsidyService->getApplicationSubsidyResource($application, $readOnly);
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
    public function getApplicationRequestFilterForUserResource(Request $request): ApplicationRequestsFilterResource
    {
        return $this->applicationService->getApplicationRequestFilterResource($request->user());
    }

    /**
     * Get the filter UI resource.
     */
    public function getApplicationRequestFilterResource(Request $request): ApplicationRequestsFilterResource
    {
        return $this->applicationService->getApplicationRequestFilterResource($request->user());
    }

    public function getApplicationHistory(Application $application): ResourceCollection
    {
        $this->authorize('getApplicationHistory', $application);

        return $this->applicationService->getApplicationStagesResource($application);
    }

    public function getApplicationReviewer(): JsonResource
    {
        //TODO: implement this
        return JsonResource::make([]);
    }

    public function getApplicationTransitions(Application $application): ResourceCollection
    {
        $this->authorize('getTransitionHistory', $application);

        return $this->applicationService->getApplicationStageTransitions($application);
    }

    public function getLetterForMessage(ApplicationMessage $message): Response
    {
        $this->authorize('getLetterFromMessage', $message->application);

        return $this->applicationService->getLetterFromMessage($message, MessageDownloadFormat::PDF);
    }

    /**
     * @throws Throwable
     */
    public function saveAssessment(
        string $applicationId,
        Authenticatable $user,
        Request $request,
    ): ApplicationSubsidyVersionResource {
        return DB::transaction(fn() => $this->doSaveAssessment($applicationId, $user, $request));
    }

    /**
     * @throws AuthorizationException
     * @throws ApplicationFlowException
     * @throws ModelNotFoundException
     */
    public function doSaveAssessment(
        string $applicationId,
        Authenticatable $user,
        Request $request
    ): ApplicationSubsidyVersionResource {
        $application = $this->applicationRepository->getApplication($applicationId, lockForUpdate: true);

        if (is_null($application)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('save', $application);

        $submittedData = $request->json()->all();
        $data = (object)($submittedData['data'] ?? []);
        $submit = (bool)($submittedData['submit'] ?? false);

        assert($user instanceof User);

        try {
            $application = $this->applicationService->saveAssessment($application, $data, $submit);

            if ($submit) {
                $this->logService->log((new SubmitAssessmentEvent())
                    ->withActor($user)
                    ->withData([
                        'applicationId' => $application->id,
                        'userId' => $user->getAuthIdentifier(),
                    ]));
            }

            return $this->applicationSubsidyService->getApplicationSubsidyResource($application, true);
        } catch (InvalidApplicationSaveException) {
            abort(Response::HTTP_FORBIDDEN);
        } catch (ValidationErrorException $exception) {
            Log::error('ValidationErrorException while saveAssessment', [
                'userId' => $user->id,
                'applicationId' => $application->id,
                'submitAssessment' => $submit,
                'validationErrors' => $exception->getValidationResults(),
            ]);
            abort(Response::HTTP_BAD_REQUEST);
        }
    }

    public function previewTransition(
        Application $application,
        Authenticatable $user
    ): ApplicationSubsidyVersionResource {
        $this->authorize('previewTransition', $application);

        assert($user instanceof User);

        try {
            return $this->applicationSubsidyService->getApplicationTransitionPreview($application);
        } catch (TransitionNotFoundException) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @throws Throwable
     */
    public function submitAssessment(
        string $applicationId,
        Authenticatable $user,
    ): ApplicationSubsidyVersionResource {
        return DB::transaction(fn() => $this->doSubmitAssessment($applicationId, $user));
    }

    /**
     * @throws AuthorizationException
     * @throws ApplicationFlowException
     */
    public function doSubmitAssessment(
        string $applicationId,
        Authenticatable $user,
    ): ApplicationSubsidyVersionResource {
        $application = $this->applicationRepository->getApplication($applicationId, lockForUpdate: true);

        if (is_null($application)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('submit', $application);

        assert($user instanceof User);

        try {
            $application = $this->applicationService->submitAssessment($application);

            $this->logService->log((new SubmitAssessmentEvent())
                ->withActor($user)
                ->withData([
                    'applicationId' => $application->id,
                    'userId' => $user->getAuthIdentifier(),
                ]));

            return $this->applicationSubsidyService->getApplicationSubsidyResource($application, true);
        } catch (InvalidApplicationSubmitException) {
            abort(Response::HTTP_FORBIDDEN);
        } catch (ValidationErrorException $exception) {
            Log::error('ValidationErrorException while submitAssessment', [
                'userId' => $user->id,
                'applicationId' => $application->id,
                'validationErrors' => $exception->getValidationResults(),
            ]);
            abort(Response::HTTP_BAD_REQUEST);
        }
    }

    public function getFilteredApplications(
        ApplicationRequest $request,
        bool $onlyMyApplications
    ): AnonymousResourceCollection {
        $user = $request->user();
        assert($user !== null);

        $filter = ApplicationsFilter::fromArray($request->validated());
        $sortOptions = SortOptions::fromString($request->validated('sort'));
        $paginationOptions = PaginationOptions::fromArray($request->safe(['page', 'per_page']));

        return $this->applicationService->getApplications(
            user: $user,
            onlyMyApplications: $onlyMyApplications,
            applicationsFilter: $filter,
            paginationOptions: $paginationOptions,
            sortOptions: $sortOptions,
        );
    }
}
