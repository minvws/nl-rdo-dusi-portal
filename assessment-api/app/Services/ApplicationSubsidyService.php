<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Auth;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Shared\Application\Events\ViewAssignmentEvent;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;
use MinVWS\Logging\Laravel\LogService;

readonly class ApplicationSubsidyService
{
    public function __construct(
        private ApplicationDataService $applicationDataService,
        private SubsidyStageDataSchemaBuilder $dataSchemaBuilder,
        private LogService $logger,
    ) {
    }

    public function getApplicationSubsidyResource(
        Application $application
    ): ApplicationSubsidyVersionResource {
        $this->logger->log((new ViewAssignmentEvent())
        ->withData([
            'applicationId' => $application->id,
            //TODO: Update this to withActor and a loggable user object when user login is finished
            /** @phpstan-ignore-next-line */
            'userId' => Auth::User()?->id,
        ]));
        return new ApplicationSubsidyVersionResource(
            $application,
            $this->applicationDataService,
            $this->dataSchemaBuilder
        );
    }
}
