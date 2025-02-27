<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationTransitionService;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

/**
 *  @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationTransitionPreviewResource extends ApplicationSubsidyVersionResource
{
    public function __construct(
        Application $application,
        private readonly SubsidyStageTransition $transition,
        string $citizenServiceNumber,
        private readonly ?string $messageHtml,
        ApplicationDataService $applicationDataService,
        SubsidyStageDataSchemaBuilder $dataSchemaBuilder,
        private readonly ApplicationTransitionService $applicationTransitionService
    ) {
        parent::__construct(
            $application,
            true,
            $citizenServiceNumber,
            $applicationDataService,
            $dataSchemaBuilder
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return array{application: array<mixed>, applicationStages: array<mixed>, transition: array<mixed>}
     */
    public function toArray(Request $request): array
    {
        $result = parent::toArray($request);
        $result['transition'] = $this->buildTransition();
        return $result;
    }

    /**
     * @return array<mixed>
     */
    private function buildTransition(): array
    {
        $newDeadline =
            $this->applicationTransitionService->getFinalReviewDeadline($this->transition, $this->application);
        $hasNewDeadline =
            $newDeadline !== false
            && $this->application->final_review_deadline?->getTimestamp() !== $newDeadline?->getTimestamp();

        $result = [
            'current' => [
                'application' => [
                    'status' => $this->application->status->value
                ],
                'subsidyStage' => [
                    'title' => $this->transition->currentSubsidyStage->title,
                    'stage' => $this->transition->currentSubsidyStage->stage
                ],
                'deadline' => $hasNewDeadline ? $this->application->final_review_deadline?->format('Y-m-d') : null
            ],
            'target' => [
                'application' => [
                    'status' =>
                        $this->transition->target_application_status?->value ??
                        $this->application->status->value
                ],
                'subsidyStage' => [
                    'title' =>
                        $this->transition->targetSubsidyStage?->title ??
                        $this->transition->currentSubsidyStage->title,
                    'stage' =>
                        $this->transition->targetSubsidyStage?->stage ??
                        $this->transition->currentSubsidyStage->stage,
                ],
                'deadline' => $hasNewDeadline ? $newDeadline?->format('Y-m-d') : null
            ]
        ];

        if ($this->transition->send_message && isset($this->messageHtml)) {
            $result['message'] = ['html' => $this->messageHtml];
        }

        return $result;
    }
}
