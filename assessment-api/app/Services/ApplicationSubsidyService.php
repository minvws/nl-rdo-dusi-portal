<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class ApplicationSubsidyService
{
    public function __construct(private SubsidyRepository $subsidyRepository)
    {
    }

    /**
     * @param Application $application
     * @return ApplicationSubsidyVersionResource
     * @throws \Exception
     */
    public function getApplicationSubsidyResource(Application $application): ApplicationSubsidyVersionResource
    {
        $subsidyVersion = $this->subsidyRepository->getSubsidyVersion($application->subsidy_version_id);
        if (!isset($subsidyVersion)) {
            throw new \Exception('Subsidy version should always exist');
        }
        return new ApplicationSubsidyVersionResource($application, $subsidyVersion);
    }
}
