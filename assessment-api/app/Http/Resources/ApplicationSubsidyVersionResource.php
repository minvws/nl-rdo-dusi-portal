<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use MinVWS\DUSi\Assessment\API\Models\Enums\UIType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageData;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;

/**
 *  @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationSubsidyVersionResource extends JsonResource
{
    public function __construct(
        protected readonly Application $application,
        protected readonly bool $readOnly,
        protected readonly string $citizenServiceNumber,
        protected readonly ApplicationDataService $applicationDataService,
        protected readonly SubsidyStageDataSchemaBuilder $dataSchemaBuilder
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
        $surePayCloseMatchSuggestion = $this->getCloseMatchSuggestion();

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
                    'validTo' => $this->application->subsidyVersion->subsidy->valid_to?->format('Y-m-d')
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
                    ],
                    'citizenServiceNumber' => [
                        'type' => 'string',
                    ],
                    'surePayResult' => [
                        'type' => 'string'
                    ],
                    'surePayCloseMatchSuggestion' => [
                        'type' => 'string'
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
                                'Uiterste behandeldatum' => '{finalReviewDeadline}',
                                'BSN aanvrager' => '{citizenServiceNumber}',
                                'SurePay controle resultaat' => '{surePayResult}',
                                'SurePay suggestie' => '{surePayCloseMatchSuggestion}'
                            ]
                        ]
                    ]
                ]
            ],
            'data' => [
                'reference' => $this->application->reference,
                'status' => $this->application->status->name,
                'submittedAt' => $this->application->submitted_at?->format('Y-m-d'),
                'finalReviewDeadline' => $this->application->final_review_deadline?->format('Y-m-d'),
                'citizenServiceNumber' => $this->citizenServiceNumber,
                'surePayResult' =>
                    match ($this->application->applicationSurePayResult?->name_match_result) {
                        NameMatchResult::Match => 'Goed',
                        NameMatchResult::CloseMatch => 'Close match',
                        NameMatchResult::NoMatch => 'Geen match',
                        NameMatchResult::CouldNotMatch => 'Could not match',
                        NameMatchResult::NameTooShort => 'Naam te kort',
                        default => 'Onbekend'
                    },
                'surePayCloseMatchSuggestion' => $surePayCloseMatchSuggestion
            ]
        ];
    }

    private function buildApplicationStages(): array
    {
        $stage = $this->application->currentApplicationStage ?? $this->application->lastApplicationStage;

        return array_map(
            fn (ApplicationStageData $applicationStageData) => $this->buildApplicationStage($applicationStageData),
            $this->applicationDataService->getApplicationStageDataUpToIncluding($stage, $this->readOnly)
        );
    }

    private function buildApplicationStage(ApplicationStageData $applicationStageData): array
    {
        $applicationStage = $applicationStageData->applicationStage;
        $subsidyStage = $applicationStage->subsidyStage;
        $data = $applicationStageData->data;

        if ($applicationStage->is_current && !$this->readOnly) {
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

    public function getCloseMatchSuggestion(): string
    {
        return $this->application->applicationSurePayResult?->encrypted_name_suggestion ?
            $this->applicationDataService->decryptForApplicantStage(
                $this->application,
                $this->application->applicationSurePayResult->encrypted_name_suggestion
            ) : "-";
    }
}
