<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationFilterResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;

class ApplicationService
{
    public function __construct(private ApplicationRepository $applicationRepository)
    {
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
}
