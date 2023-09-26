<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationCountResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationMessageFilterResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationRequestsFilterResource;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationService
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private SubsidyRepository $subsidyRepository
    ) {
    }

    /**
     * @param ApplicationsFilter $applicationsFilter
     * @return AnonymousResourceCollection
     */
    public function getApplications(ApplicationsFilter $applicationsFilter): AnonymousResourceCollection
    {
        return ApplicationFilterResource::Collection($this->applicationRepository
            ->filterApplications($applicationsFilter));
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
        if ($user) {
            Log::debug("Fetching application request filter for user {$user->id}");
            throw new Exception("Not Implemented");
        }
        $shortRegulations = $this->subsidyRepository->getActiveSubsidyCodes();
        return ApplicationRequestsFilterResource::make(['shortRegulations' => $shortRegulations]);
    }

    public function assignToMe(User $user): Resource
    {

    }
}
