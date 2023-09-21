<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource2;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;

readonly class ApplicationSubsidyService
{
    public function __construct(
        private ApplicationDataService $applicationDataService,
        private SubsidyStageDataSchemaBuilder $dataSchemaBuilder
    ) {
    }

    public function getApplicationSubsidyResource(
        Application $application
    ): ApplicationSubsidyVersionResource {
        return new ApplicationSubsidyVersionResource(
            $application,
            $this->applicationDataService,
            $this->dataSchemaBuilder
        );
    }
}
