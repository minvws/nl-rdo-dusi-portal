<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageData;
use MinVWS\DUSi\Shared\Application\Events\ApplicationMessageEvent;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;
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
        private readonly ApplicationFileManager $applicationFileManager
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
        return $this->evaluateApplicationStage($stage, EvaluationTrigger::Submit);
    }

    /**
     * Transitions the given application stage to the next stage based on the given evaluation trigger
     * and conditions based on the stage data.
     *
     * @param ApplicationStage $stage Current stage.
     *
     * @return ApplicationStage|null Next stage (null if finished).
     *
     * @throws ApplicationFlowException
     */
    public function evaluateApplicationStage(
        ApplicationStage $stage,
        EvaluationTrigger $evaluationTrigger
    ): ?ApplicationStage {
        return DB::transaction(fn () => $this->doEvaluateApplicationStage($stage, $evaluationTrigger));
    }

    private function doEvaluateApplicationStage(
        ApplicationStage $stage,
        EvaluationTrigger $evaluationTrigger
    ): ?ApplicationStage {
        if ($stage->isDirty()) {
            $stage->save();
        }

        $stage->refresh();

        if (!$stage->is_current) {
            throw new ApplicationFlowException('Given stage is not the current stage!');
        }

        $transition = $this->evaluateTransitionsForApplicationStage($stage, $evaluationTrigger);
        if ($transition === null) {
            throw new ApplicationFlowException('No matching transition found for submit!');
        }

        $this->closeCurrentApplicationStage($stage, $evaluationTrigger);

        $stage->application->touch();

        return $this->performTransitionForApplicationStage($transition, $stage);
    }

    private function closeCurrentApplicationStage(ApplicationStage $stage, EvaluationTrigger $evaluationTrigger): void
    {
        $stage->is_submitted = $evaluationTrigger === EvaluationTrigger::Submit;
        $stage->submitted_at = Carbon::now();
        $stage->is_current = false;
        $stage->expires_at = null;
        $this->applicationRepository->saveApplicationStage($stage);

        // delete answers and files if the user did not explicitly submit
        if ($evaluationTrigger !== EvaluationTrigger::Submit) {
            $this->applicationRepository->deleteAnswersByStage($stage);
            $this->applicationFileManager->deleteDirectory($stage);
        }
    }

    public function evaluateTransitionsForApplicationStage(
        ApplicationStage $stage,
        EvaluationTrigger $evaluationTrigger
    ): ?SubsidyStageTransition {
        $transitions =
            $stage->subsidyStage->subsidyStageTransitions
                ->filter(
                    fn (SubsidyStageTransition $transition) =>
                        $transition->evaluation_trigger === $evaluationTrigger
                );
        foreach ($transitions as $transition) {
            if ($this->evaluateTransitionForApplicationStage($transition, $stage)) {
                return $transition;
            }
        }

        return null;
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
        SubsidyStageTransition $subsidyStageTransition,
        ApplicationStage $currentStage
    ): ?ApplicationStage {
        $application = $currentStage->application;
        $currentApplicationStatus = $application->status;
        $this->performTransitionForApplication($subsidyStageTransition, $application);
        $newApplicationStatus = $application->status;
        $newStage = $this->createNextApplicationStageForTransition($subsidyStageTransition, $currentStage);
        $transition = $this->createApplicationStageTransition(
            $subsidyStageTransition,
            $application,
            $currentStage,
            $currentApplicationStatus,
            $newStage,
            $newApplicationStatus
        );
        $this->scheduleMessageForApplicationStageTransition($transition);
        return $newStage;
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
        // 3. Add any subsequent stages where the application was returned to the applicant.
        $stages = $this->applicationRepository->getOrderedSubmittedApplicationStagesForSubsidyStage(
            $application,
            $transition->currentSubsidyStage
        );

        assert(count($stages) > 0);
        assert($stages[0]->submitted_at !== null);

        $deadline = Carbon::instance(array_shift($stages)->submitted_at)
            ->addDays($application->subsidyVersion->review_period);

        foreach ($stages as $stage) {
            $timeAtApplicant = Carbon::instance($stage->created_at)->diff($stage->submitted_at);
            $deadline->add($timeAtApplicant);
        }

        $application->final_review_deadline = $deadline->endOfDay()->floorSecond();

        return true;
    }

    private function scheduleMessageForApplicationStageTransition(ApplicationStageTransition $transition): void
    {
        if (
            !$transition->subsidyStageTransition->send_message ||
            $transition->subsidyStageTransition->publishedSubsidyStageTransitionMessage === null
        ) {
            return;
        }

        $message = $transition->subsidyStageTransition->publishedSubsidyStageTransitionMessage;

        DB::afterCommit(function () use ($message, $transition) {
            ApplicationMessageEvent::dispatch($message, $transition);
        });
    }

    private function cloneApplicationStageData(ApplicationStage $source, ApplicationStage $target): void
    {
        $target->encrypted_key = $source->encrypted_key;
        $this->applicationRepository->cloneApplicationStageAnswers($source, $target);
        $this->applicationFileManager->copyFiles($source, $target);
    }

    private function assignApplicationStageToPreviousAssessor(ApplicationStage $source, ApplicationStage $target): void
    {
        $previousAssessor = $source->assessorUser;
        if ($previousAssessor === null || !$previousAssessor->active) {
            // user not active anymore
            return;
        }

        $role = $target->subsidyStage->assessor_user_role;
        $subsidy = $target->application->subsidyVersion->subsidy;
        if ($role === null || !$previousAssessor->hasRoleForSubsidy($role, $subsidy)) {
            // user doesn't have required role anymore
            return;
        }

        $stages = $this->applicationRepository->getLatestApplicationStagesUpToIncluding($target);
        foreach ($stages as $stage) {
            if ($stage->assessor_user_id === $previousAssessor->id) {
                // assessor already picked up an earlier (active!) stage, not allowed to assess 2 stages
                return;
            }
        }

        $target->assessor_user_id = $previousAssessor->id;
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
        if ($transition->clone_data || $transition->assign_to_previous_assessor) {
            $previousInstanceOfTargetStage =
                $this->applicationRepository->getLatestSubmittedApplicationStageForSubsidyStage(
                    $currentStage->application,
                    $targetSubsidyStage
                );
        }

        $expiresAt =
            $transition->expiration_period === null ? null : Carbon::now()->addDays($transition->expiration_period);

        $stage = $this->applicationRepository->makeApplicationStage($currentStage->application, $targetSubsidyStage);
        $stage->sequence_number = $currentStage->sequence_number + 1;
        $stage->is_submitted = false;
        $stage->is_current = true;
        $stage->expires_at = $expiresAt;
        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $stage->encrypted_key = $encryptedKey;

        $this->applicationRepository->saveApplicationStage($stage);

        if ($transition->clone_data && isset($previousInstanceOfTargetStage)) {
            $this->cloneApplicationStageData($previousInstanceOfTargetStage, $stage);
        }

        if ($transition->assign_to_previous_assessor && isset($previousInstanceOfTargetStage)) {
            $this->assignApplicationStageToPreviousAssessor($previousInstanceOfTargetStage, $stage);
        }

        $this->applicationRepository->saveApplicationStage($stage);

        return $stage;
    }

    private function createApplicationStageTransition(
        SubsidyStageTransition $subsidyStageTransition,
        Application $application,
        ApplicationStage $previousApplicationStage,
        ApplicationStatus $previousApplicationStatus,
        ?ApplicationStage $newApplicationStage,
        ApplicationStatus $newApplicationStatus
    ): ApplicationStageTransition {
        return $this->applicationRepository->createApplicationStageTransition(
            $subsidyStageTransition,
            $application,
            $previousApplicationStage,
            $previousApplicationStatus,
            $newApplicationStage,
            $newApplicationStatus
        );
    }
}
