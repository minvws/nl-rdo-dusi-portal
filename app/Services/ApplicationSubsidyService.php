<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\ApplicationSubsidyVersionResource;
use App\Models\Submission\FieldValue;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class ApplicationSubsidyService
{
    public function __construct(private SubsidyRepository $subsidyRepository)
    {
    }

    public function getApplicationSubsidyResource(Application $application)
    {
        $subsidyVersion = $this->subsidyRepository->getSubsidyVersion($application->subsidy_version_id);
        return new ApplicationSubsidyVersionResource($application, $subsidyVersion);
    }
}
