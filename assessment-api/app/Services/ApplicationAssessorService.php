<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Http\Resources\AssessorPoolUserResource;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidAssignmentException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\InvalidReleaseException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\User\Repositories\UserRepository;
use Throwable;

readonly class ApplicationAssessorService
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @throws InvalidAssignmentException|Throwable
     */
    public function assignApplicationByUserId(Application $application, string $assessorId): void
    {
        $user = $this->userRepository->find($assessorId);
        if ($user === null) {
            Log::debug('User does not exist');
            throw new InvalidAssignmentException();
        }
        $applicationStages = $application->applicationStages;

        $allow_duplicate_assessors = $applicationStages
            ->filter(fn($as) => $as->is_current)
            ->firstOrFail()
            ->subsidyStage
            ->allow_duplicate_assessors;

        foreach ($applicationStages as $applicationStage) {
            if (!$allow_duplicate_assessors && $applicationStage->assessor_user_id === $user->id) {
                Log::debug('User already assessed a stage');
                throw new InvalidAssignmentException();
            }
        }
        $this->assignApplication($application, $user);
    }

    /**
     * @throws InvalidAssignmentException|Throwable
     */
    public function assignApplication(Application $application, User $user): void
    {
        DB::transaction(function () use ($application, $user) {
            $stage = $application->currentApplicationStage;
            if ($stage === null) {
                throw new InvalidAssignmentException();
            }

            $this->applicationRepository->assignApplicationStage($stage, $user);
        });
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

    public function getAssessorPool(Application $application, string|null $search): AnonymousResourceCollection
    {
        $previousAssessorIds = [];
        if(!$application->currentApplicationStage->subsidyStage->allow_duplicate_assessors){
            $previousAssessorIds = $application->applicationStages
                ->pluck('assessor_user_id')
                ->filter()
                ->toArray();
        }

        if ($application->currentApplicationStage === null) {
            return AssessorPoolUserResource::collection([]);
        }

        $users = $this->userRepository->getPotentialUsersWithSpecificRole(
            $application->currentApplicationStage->subsidyStage,
            $previousAssessorIds,
            $search
        );
        return AssessorPoolUserResource::collection($users);
    }
}
