<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class ApplicationSubsidyService
{
    public function __construct(
        private SubsidyRepository $subsidyRepository,
        private EncryptionService $encryptionService
    ) {
    }

    /**
     * @param Application $application
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
            $this->encryptionService
        );
    }
}
