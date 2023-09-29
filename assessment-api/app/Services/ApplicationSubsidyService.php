<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;
use MinVWS\DUSi\Shared\User\Models\User;

readonly class ApplicationSubsidyService
{
    public function __construct(
        private ApplicationDataService $applicationDataService,
        private SubsidyStageDataSchemaBuilder $dataSchemaBuilder,
        private HsmDecryptionService $hsmDecryptionService
    ) {
    }

    public function getApplicationSubsidyResource(
        Application $application,
        User $user
    ): ApplicationSubsidyVersionResource {
        return new ApplicationSubsidyVersionResource(
            $application,
            $user,
            $this->applicationDataService,
            $this->dataSchemaBuilder,
            $this->hsmDecryptionService
        );
    }
}
