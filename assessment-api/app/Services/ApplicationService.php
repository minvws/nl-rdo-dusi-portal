<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationCountResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationMessageFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationRequestsFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationStageResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationStageTransitionResource;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidApplicationSaveException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidApplicationSubmitException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\PaginationOptions;
use MinVWS\DUSi\Shared\Application\DTO\SortColumn;
use MinVWS\DUSi\Shared\Application\DTO\SortOptions;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ValidationErrorException;
use MinVWS\DUSi\Shared\Application\Services\ValidationService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationService
{
    private const FINAL_REVIEW_DEADLINE_COLUMN = 'final_review_deadline';
    private const UPDATED_AT_COLUMN = 'updated_at';

    public function __construct(
        private ApplicationDataService $applicationDataService,
        private ApplicationFlowService $applicationFlowService,
        private ValidationService $validationService,
        private ApplicationRepository $applicationRepository,
        private SubsidyRepository $subsidyRepository,
        private ApplicationFileService $applicationFileService,
    ) {
    }

    /**
     * @param ApplicationsFilter $applicationsFilter
     * @return AnonymousResourceCollection
     */
    public function getApplications(
        User $user,
        bool $onlyMyApplications,
        ApplicationsFilter $applicationsFilter,
        PaginationOptions $paginationOptions,
        SortOptions $sortOptions,
    ): AnonymousResourceCollection {
        if (!$sortOptions->hasSortColumns()) {
            $sortOptions = new SortOptions([
                new SortColumn(self::FINAL_REVIEW_DEADLINE_COLUMN, true),
            ]);
        }
        if (!$sortOptions->containsColumn(self::UPDATED_AT_COLUMN)) {
            $sortOptions = $sortOptions->append(new SortColumn(self::UPDATED_AT_COLUMN, true));
        }

        $applications = $this->applicationRepository
            ->filterApplicationsPaginated(
                user: $user,
                onlyMyApplications: $onlyMyApplications,
                filter: $applicationsFilter,
                paginationOptions: $paginationOptions,
                sortOptions: $sortOptions,
            );

        return ApplicationFilterResource::collection($applications)->additional([
            'meta' => [
                ...$this->getResourceSortMetadata($sortOptions),
            ],
        ]);
    }

    public function getApplicationsCountMock(): ApplicationCountResource
    {
        return ApplicationCountResource::make(1, 2, 3, 4);
    }

    public function getApplicationMessageFilterResource(): ApplicationMessageFilterResource
    {
        $shortRegulations = $this->subsidyRepository->getActiveSubsidyCodes();

        return ApplicationMessageFilterResource::make(['shortRegulations' => $shortRegulations]);
    }

    /**
     * @throws Exception
     */
    public function getApplicationRequestFilterResource(?User $user): ApplicationRequestsFilterResource
    {
        $userSubsidies = $user?->roles()->pluck('subsidy_id')->toArray() ?? [];

        // If user has null in subsidy_id for a role, they can see all subsidies
        if (in_array(null, $userSubsidies, true)) {
            $userSubsidies = null;
        }

        $shortRegulations = $this->subsidyRepository->getActiveSubsidyCodes($userSubsidies);
        $phases = $this->subsidyRepository->getSubsidyStageTitles($userSubsidies);

        return ApplicationRequestsFilterResource::make(['shortRegulations' => $shortRegulations, 'phases' => $phases]);
    }

    public function getApplicationStagesResource(Application $application): ResourceCollection
    {
        return ApplicationStageResource::collection($application->applicationStages);
    }

    /**
     * @throws InvalidApplicationSaveException
     * @throws ApplicationFlowException
     * @throws ValidationErrorException
     */
    public function saveAssessment(Application $application, object $data, bool $submit): Application
    {
        $stage = $application->currentApplicationStage;
        if ($stage === null || $stage->is_submitted) {
            throw new InvalidApplicationSaveException();
        }

        $this->applicationDataService->saveApplicationStageData($stage, $data, $submit);
        if ($submit) {
            $this->applicationFlowService->submitApplicationStage($stage);
        }

        $application->refresh();

        return $application;
    }

    /**
     * @throws InvalidApplicationSubmitException
     * @throws ApplicationFlowException
     * @throws ValidationErrorException
     */
    public function submitAssessment(Application $application): Application
    {
        $stage = $application->currentApplicationStage;
        if ($stage === null || $stage->is_submitted) {
            throw new InvalidApplicationSubmitException();
        }

        $fieldValues = $this->applicationDataService->getApplicationStageDataAsFieldValues($stage);
        $validator = $this->validationService->getValidator($stage, $fieldValues, true);
        $validator->validate();

        $this->applicationFlowService->submitApplicationStage($stage);

        $application->refresh();

        return $application;
    }

    public function getApplicationStageTransitions(Application $application): ResourceCollection
    {
        return ApplicationStageTransitionResource::collection($application->applicationStageTransitions);
    }

    /**
     * @throws Exception
     */
    public function getLetterFromMessage(ApplicationMessage $message, MessageDownloadFormat $format): Response
    {
        return $this->applicationFileService->getMessageFile($message, $format);
    }

    protected function getResourceSortMetadata(SortOptions $sortOptions): array
    {
        $sorts = [];

        if ($sortOptions->hasSortColumns()) {
            foreach ($sortOptions->getSortColumns() as $sortColumn) {
                $sorts[] = [
                    'column' => $sortColumn->getColumn(),
                    'direction' => $sortColumn->isAscending() ? 'ascending' : 'descending',
                ];
            }
        }

        return [
            'sorts' => $sorts
        ];
    }
}
