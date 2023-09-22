<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageData;
use MinVWS\DUSi\Shared\Application\Events\ApplicationMessageEvent;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationFlowService
{
    public function __construct(
        private readonly ApplicationDataService $applicationDataService,
        private readonly ApplicationStageEncryptionService $encryptionService,
        private readonly ApplicationRepository $applicationRepository,
        private readonly ApplicationFileRepositoryService $applicationFileRepository
    ) {
    }

    /**
     * Submits / closes the given application stage and evaluates the conditions to
     * determine the next stage, if any.
     *
     * @param ApplicationStage $stage Current stage.
     *
     * @return ApplicationStage|null Next stage (null if finished).
     *
     * @throws ApplicationFlowException
     */
    public function submitApplicationStage(ApplicationStage $stage): ?ApplicationStage
    {
        return DB::transaction(fn () => $this->doSubmitApplicationStage($stage));
    }

    private function doSubmitApplicationStage(ApplicationStage $stage): ?ApplicationStage
    {
        if ($stage->isDirty()) {
            $stage->save();
        }

        $stage->refresh();

        if (!$stage->is_current) {
            throw new ApplicationFlowException('Given stage is not the current stage!');
        }

        $this->closeCurrentApplicationStage($stage);

        $transitions = $stage->subsidyStage->subsidyStageTransitions;
        foreach ($transitions as $transition) {
            if ($this->evaluateTransitionForApplicationStage($transition, $stage)) {
                return $this->performTransitionForApplicationStage($transition, $stage);
            }
        }

        return null;
    }

    private function closeCurrentApplicationStage(ApplicationStage $stage): void
    {
        $stage->is_submitted = true;
        $stage->submitted_at = Carbon::now();
        $stage->is_current = false;
        $this->applicationRepository->saveApplicationStage($stage);
    }

    private function evaluateTransitionForApplicationStage(
        SubsidyStageTransition $transition,
        ApplicationStage $stage
    ): bool {
        if ($transition->condition === null) {
            return true;
        }

        $data = array_map(
            fn (ApplicationStageData $stageData) => $stageData->data,
            $this->applicationDataService->getApplicationStageDataUpToIncluding($stage)
        );

        return $transition->condition->evaluate($data);
    }

    private function performTransitionForApplicationStage(
        SubsidyStageTransition $transition,
        ApplicationStage $currentStage
    ): ?ApplicationStage {
        $this->performTransitionForApplication($transition, $currentStage->application);
        $this->scheduleMessageForClosedApplicationStage($transition, $currentStage);
        return $this->createNextApplicationStageForTransition($transition, $currentStage);
    }

    private function performTransitionForApplication(SubsidyStageTransition $transition, Application $application): void
    {
        $save = $this->updateFinalReviewDeadline($transition, $application);

        if (isset($transition->target_application_status)) {
            $application->status = $transition->target_application_status;
            $save = true;
        }

        if ($save) {
            $this->applicationRepository->saveApplication($application);
        }
    }

    private function updateFinalReviewDeadline(
        SubsidyStageTransition $transition,
        Application $application
    ): bool {
        if ($transition->targetSubsidyStage?->subject_role === SubjectRole::Applicant) {
            // Returning to the applicant, clear review deadline.
            $application->final_review_deadline = null;
            return true;
        }

        if ($transition->currentSubsidyStage->subject_role !== SubjectRole::Applicant) {
            // Only need to calculate a new final review deadline if we transition from
            // the applicant stage.
            return false;
        }

        if (isset($application->subsidyVersion->review_deadline)) {
            $application->final_review_deadline = $application->subsidyVersion->review_deadline;
            return true;
        }

        // either review deadline or review period is set
        assert($application->subsidyVersion->review_period !== null);

        // Calculate final review deadline:
        // 1. Take the first submit date from the applicant.
        // 2. Add the review period.
        // 3. Distract any subsequent stages where the application was returned to the applicant.
        $stages = $this->applicationRepository->getOrderedApplicationStagesForSubsidyStage(
            $application,
            $transition->currentSubsidyStage
        );

        assert(count($stages) > 0);
        assert($stages[0]->submitted_at !== null);

        $deadline =
            CarbonImmutable::instance(array_shift($stages)->submitted_at)
                ->addDays($application->subsidyVersion->review_period);

        $diff = null;
        foreach ($stages as $stage) {
            $current = Carbon::instance($stage->created_at)->diff($stage->submitted_at);
            $diff = $diff === null ? $current : $diff->add($current);
        }

        if ($diff !== null) {
            $deadline = $deadline->add($diff);
        }

        $application->final_review_deadline = $deadline->endOfDay()->floorSecond();

        return true;
    }

    private function scheduleMessageForClosedApplicationStage(
        SubsidyStageTransition $transition,
        ApplicationStage $closedStage
    ): void {
        if (!$transition->send_message || $transition->publishedSubsidyStageTransitionMessage === null) {
            return;
        }

        // TODO: Make sure the message is generated after the transition has been successfully saved to the database
        ApplicationMessageEvent::dispatch(
            $transition->publishedSubsidyStageTransitionMessage,
            $closedStage
        );
    }

    private function createNextApplicationStageForTransition(
        SubsidyStageTransition $transition,
        ApplicationStage $currentStage
    ): ?ApplicationStage {
        if (!isset($transition->targetSubsidyStage)) {
            return null;
        }

        $targetSubsidyStage = $transition->targetSubsidyStage;

        $previousInstanceOfTargetStage = null;
        if ($transition->clone_data) {
            $previousInstanceOfTargetStage = $this->applicationRepository->getLatestApplicationStageForSubsidyStage(
                $currentStage->application,
                $targetSubsidyStage
            );
        }

        $stage = $this->applicationRepository->makeApplicationStage($currentStage->application, $targetSubsidyStage);
        $stage->sequence_number = $currentStage->sequence_number + 1;
        $stage->is_submitted = false;
        $stage->is_current = true;

        if (isset($previousInstanceOfTargetStage)) {
            $stage->encrypted_key = $previousInstanceOfTargetStage->encrypted_key;
        } else {
            [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
            $stage->encrypted_key = $encryptedKey;
        }

        $this->applicationRepository->saveApplicationStage($stage);

        if (isset($previousInstanceOfTargetStage)) {
            $this->applicationRepository->cloneApplicationStageAnswers($previousInstanceOfTargetStage, $stage);
            $this->applicationFileRepository->copyFiles($previousInstanceOfTargetStage, $stage);
        }

        return $stage;
    }
}
