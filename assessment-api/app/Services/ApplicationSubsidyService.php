<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;
use MinVWS\DUSi\Shared\User\Models\User;
use Throwable;

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
        try {
            $citizenServiceNumber =
                $this->hsmDecryptionService->decrypt($application->identity->encrypted_identifier);
        } catch (Throwable $e) {
            Log::error('Error trying to decrypt citizen service number: ' . $e->getMessage());
            $citizenServiceNumber = 'Onbekend';
        }

        return new ApplicationSubsidyVersionResource(
            $application,
            $citizenServiceNumber,
            $user,
            $this->applicationDataService,
            $this->dataSchemaBuilder
        );
    }
}
