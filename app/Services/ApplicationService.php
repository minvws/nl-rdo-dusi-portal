<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\ApplicationFilterResource;
use App\Http\Resources\ApplicationSubsidyVersionResource;
use App\Models\Submission\FieldValue;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class ApplicationService
{
    public function __construct(private ApplicationRepository $applicationRepository)
    {
    }

    public function getApplications(ApplicationsFilter $applicationsFilter)
    {
        return ApplicationFilterResource::Collection($this->applicationRepository
            ->filterApplications($applicationsFilter));
    }
}
