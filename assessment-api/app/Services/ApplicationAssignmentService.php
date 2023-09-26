<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Exception;
use MinVWS\DUSi\Assessment\API\Models\User;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;

readonly class ApplicationAssignmentService
{
    public function __construct(
        private ApplicationRepository $applicationRepository
    ) {
    }

    public function assignApplication(Application $application, User $user): void
    {
        DB::transaction(function () use ($application, $user) {
            $stage = $application->currentApplicationStage;
            if ($stage === null || $stage->subsidyStage->subject_role !== SubjectRole::Assessor) {
                throw new Exception('...');
            }

            if ($stage->assessor_user_id !== null) {
                throw new Exception('...');
            }

            $this->applicationRepository->assignApplication($application, $user);
        });
    }

    public function releaseApplication(Application $application, User $user): void
    {
        DB::transaction(function () use ($application, $user) {
            $stage = $application->currentApplicationStage;
            if ($stage === null || $stage->assessor_user_id !== $user->id) {
                throw new Exception('...');
            }

            $this->applicationRepository->assignApplication($application, null);
        });
    }
}
