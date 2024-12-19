<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Carbon\Carbon;
use DateInterval;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\ReviewDeadlineSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

readonly class ApplicationTransitionService
{
    public function __construct(
        private ApplicationDataService $applicationDataService,
        private ApplicationRepository $applicationRepository,
    ) {
    }

    public function getFinalReviewDeadline(
        SubsidyStageTransition $transition,
        Application $application,
    ): Carbon | null | false {
        // Returning to the applicant, clear review deadline.
        if ($transition->targetSubsidyStage?->subject_role === SubjectRole::Applicant) {
            return null;
        }

        // If the current stage is the applicant and the target stage is the assessor, set the review deadline
        // because the deadline was set empty when the application was returned to the applicant.
        if (
            $transition->currentSubsidyStage->subject_role === SubjectRole::Applicant
            && $transition->targetSubsidyStage?->subject_role === SubjectRole::Assessor
        ) {
            // If fixed review deadline is set, use that.
            if (isset($application->subsidyVersion->review_deadline)) {
                return $application->subsidyVersion->review_deadline;
            }

            return $this->calculateDeadlineBasedOnApplicantPeriod($application, $transition);
        }

        // Update the review deadline if this is defined in the transition between assessors.
        if (
            $transition->currentSubsidyStage->subject_role === SubjectRole::Assessor
            && $transition->targetSubsidyStage?->subject_role === SubjectRole::Assessor
        ) {
            return $this->calculateDeadlineBasedOnTransitionSettings($application, $transition);
        }

        return false;
    }

    private function getReviewDeadlineSourceValue(
        SubsidyStageTransition $transition,
        Application $application
    ): Carbon | null {
        $reviewDeadlineSource = $transition->target_application_review_deadline_source;
        if ($reviewDeadlineSource === ReviewDeadlineSource::Now) {
            return Carbon::now();
        }

        if ($reviewDeadlineSource === ReviewDeadlineSource::ExistingDeadline) {
            $reviewDeadline = $application->final_review_deadline;
            if ($reviewDeadline === null) {
                return null;
            }

            return Carbon::instance($reviewDeadline);
        }

        if ($reviewDeadlineSource === ReviewDeadlineSource::Field) {
            return $this->getReviewDeadlineSourceFieldValue($transition, $application);
        }

        return null;
    }

    private function getReviewDeadlineSourceFieldValue(
        SubsidyStageTransition $transition,
        Application $application
    ): ?Carbon {
        $field = $transition->target_application_review_deadline_source_field;
        if ($field === null) {
            return null;
        }

        $data = $this->applicationDataService->getApplicationStageDataForFieldByFieldReference($application, $field);
        if (!is_string($data)) {
            return null;
        }

        $date = Carbon::createFromFormat("Y-m-d", $data);
        if ($date === false) {
            return null;
        }

        return $date->endOfDay()->floorSecond();
    }

    private function calculateDeadlineBasedOnApplicantPeriod(
        Application $application,
        SubsidyStageTransition $transition
    ): Carbon|false {
        // If the deadline is based on a review period, add the applicant time to the deadline.
        if ($application->subsidyVersion->review_period === null) {
            return false;
        }

        // Calculate final review deadline:
        // 1. Take the first closed date from the applicant.
        // 2. Add the review period.
        // 3. Add any subsequent stages where the application was returned to the applicant.
        $stages = $this->applicationRepository->getOrderedClosedApplicationStagesForSubsidyStage(
            $application,
            $transition->currentSubsidyStage
        );

        assert(count($stages) > 0);
        $firstStage = array_shift($stages);
        assert($firstStage->closed_at !== null);

        $deadline = Carbon::instance($firstStage->closed_at)
            ->addDays($application->subsidyVersion->review_period);

        foreach ($stages as $stage) {
            assert($stage->closed_at !== null);
            $timeAtApplicant = Carbon::instance($stage->created_at)->diff($stage->closed_at);
            $deadline->add($timeAtApplicant);
        }

        return $deadline->endOfDay()->floorSecond();
    }

    private function calculateDeadlineBasedOnTransitionSettings(
        Application $application,
        SubsidyStageTransition $transition
    ): Carbon|false {
        // Get the source review deadline.
        $deadlineAssignation = $this->getReviewDeadlineSourceValue($transition, $application);
        if ($deadlineAssignation === null) {
            return false;
        }

        // Add the additional period if set.
        $additionalPeriod = $transition->target_application_review_deadline_additional_period;
        if ($additionalPeriod !== null) {
            $deadlineAssignation->add(new DateInterval($additionalPeriod));
        }

        return $deadlineAssignation->endOfDay()->floorSecond();
    }
}
