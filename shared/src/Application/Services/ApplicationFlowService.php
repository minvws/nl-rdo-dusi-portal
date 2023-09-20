<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\Events\ApplicationMessageEvent;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

class ApplicationFlowService
{
    public function __construct(
        private readonly ApplicationDataService $applicationDataService,
        private readonly ApplicationRepository $applicationRepository
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

        $data = $this->applicationDataService->getApplicationStageDataUpToIncluding($stage);
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
        if (!isset($transition->target_application_status)) {
            return;
        }

        $application->status = $transition->target_application_status;
        $this->applicationRepository->saveApplication($application);
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
        $stage = $this->applicationRepository->makeApplicationStage($currentStage->application, $targetSubsidyStage);
        $stage->sequence_number = $currentStage->sequence_number + 1;
        $stage->is_current = true;
        $this->applicationRepository->saveApplicationStage($stage);

        return $stage;
    }
}
