<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use MinVWS\DUSi\Assessment\API\Models\Enums\UIType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageData;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;

/**
 *  @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationSubsidyVersionResource extends JsonResource
{
    public function __construct(
        private readonly Application $application,
        private readonly ApplicationDataService $applicationDataService,
        private readonly SubsidyStageDataSchemaBuilder $dataSchemaBuilder
    ) {
        parent::__construct($application);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            'application' => $this->buildApplication(),
            'applicationStages' => $this->buildApplicationStages()
        ];
    }

    private function buildApplication(): array
    {
        return [
            'metadata' => [
                'application' => [
                    'id' => $this->application->id,
                    'reference' => $this->application->reference,
                    'finalReviewDeadline' => $this->application->final_review_deadline,
                    'status' => $this->application->status->value
                ],
                'subsidyVersion' => [
                    'id' => $this->application->subsidyVersion->id,
                    'version' => $this->application->subsidyVersion->version
                ],
                'subsidy' => [
                    'id' => $this->application->subsidyVersion->subsidy->id,
                    'code' => $this->application->subsidyVersion->subsidy->code,
                    'title' => $this->application->subsidyVersion->subsidy->title,
                    'description' => $this->application->subsidyVersion->subsidy->description,
                    'validFrom' => $this->application->subsidyVersion->subsidy->valid_from->format('Y-m-d'),
                    'validTo' => $this->application->subsidyVersion->subsidy->valid_to->format('Y-m-d')
                ]
            ],
            'dataschema' => [
                'type' => 'object',
                'properties' => [
                    'reference' => ['type' => 'string'],
                    'status' => ['type' => 'string'],
                    'submittedAt' => [
                        'type' => 'string',
                        'format' => 'date'
                    ],
                    'finalReviewDeadline' => [
                        'type' => 'string',
                        'format' => 'date'
                    ]
                ]
            ],
            'uiType' => UIType::View,
            'uischema' => [
                'type' => 'FormGroupControl',
                'label' => 'Metagegevens',
                'options' => [
                    'section' => true,
                    'headingLevel' => '2'
                ],
                'elements' => [
                    [
                        'type' => 'FormResultsTable',
                        'options' => [
                            'fields' => [
                                'Dossiernummer' => '{reference}',
                                'Aangevraagd op' => '{submittedAt}',
                                'Uiterste behandeldatum' => '{finalReviewDeadline}'
                            ]
                        ]
                    ]
                ]
            ],
            'data' => [
                'reference' => $this->application->reference,
                'status' => $this->application->status->name,
                // TODO: submitted_at at the application level!
                'submittedAt' => $this->application->created_at->format('Y-m-d'),
                'finalReviewDeadline' => $this->application->final_review_deadline?->format('Y-m-d')
            ]
        ];
    }

    private function buildApplicationStages(): array
    {
        return array_map(
            fn (ApplicationStageData $applicationStageData) =>
            $this->buildApplicationStage($applicationStageData),
            $this->applicationDataService->getApplicationStageDataUpToIncluding(
                $this->application->currentApplicationStage
            )
        );
    }

    private function buildApplicationStage(ApplicationStageData $applicationStageData): array
    {
        $applicationStage = $applicationStageData->applicationStage;
        $subsidyStage = $applicationStage->subsidyStage;
        $data = $applicationStageData->data;

        if ($applicationStage->is_current) {
            $uiType = UIType::Input;
            $uiSchema = $subsidyStage->publishedUI?->input_ui;
        } else {
            $uiType = UIType::View;
            $uiSchema = $subsidyStage->publishedUI?->view_ui;
        }

        return [
            'metadata' => [
                'subsidyStage' => [
                    'id' => $subsidyStage->id,
                    'stage' => $subsidyStage->stage,
                    'title' => $subsidyStage->title,
                    'subjectRole' => $subsidyStage->subject_role
                ],
                'applicationStage' => [
                    'id' => $applicationStage->id,
                    'sequenceNumber' => $applicationStage->sequence_number,
                    'isCurrent' => $applicationStage->is_current,
                    'isSubmitted' => $applicationStage->is_submitted,
                    'submittedAt' => $applicationStage->submitted_at
                ]
            ],
            'dataschema' => $this->dataSchemaBuilder->buildDataSchema($subsidyStage),
            'uiType' => $uiType,
            'uischema' => $uiSchema,
            'data' => $data
        ];
    }
}
