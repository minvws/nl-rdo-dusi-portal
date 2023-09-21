<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

readonly class ApplicationSubsidyService
{
    public function __construct(
        private SubsidyRepository $subsidyRepository,
        private ResponseEncryptionService $responseEncryptionService,
        private ApplicationDataService $applicationDataService,
    ) {
    }

    /**
     * @param Application $application
     * @param string|null $publicKey
     * @return ApplicationSubsidyVersionResource
     * @throws \Exception
     */
    public function getApplicationSubsidyResource(
        Application $application,
        string|null $publicKey = null
    ): ApplicationSubsidyVersionResource {
        $subsidyVersion = $this->subsidyRepository->getSubsidyVersion($application->subsidy_version_id);
        if (!isset($subsidyVersion)) {
            throw new \Exception('Subsidy version should always exist');
        }
        return new ApplicationSubsidyVersionResource(
            $application,
            $subsidyVersion,
            $publicKey,
            $this->responseEncryptionService,
            $this->applicationDataService,
        );
    }
}
