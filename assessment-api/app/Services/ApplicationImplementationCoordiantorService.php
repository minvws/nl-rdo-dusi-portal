<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidReleaseException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;

readonly class ApplicationImplementationCoordiantorService
{
    public function __construct(
        private ApplicationRepository $applicationRepository
    ) {
    }


    /**
     * @throws InvalidReleaseException
     */
    public function releaseApplication(Application $application): void
    {
        DB::transaction(function () use ($application) {
            $stage = $application->currentApplicationStage;
            if ($stage === null) {
                throw new InvalidReleaseException();
            }

            $this->applicationRepository->assignApplicationStage($stage, null);
        });
    }
}
