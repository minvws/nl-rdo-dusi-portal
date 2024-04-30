<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;
use DateTime;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;

/**
 * @property string $id
 * @property string $subsidy_version_id
 * @property string $application_title
 * @property DateTime $updated_at
 * @property DateTime $final_review_deadline
 * @property Collection<ApplicationStage> $applicationStages
 */
class ApplicationRequestsFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            "uischema" => $this->toUIschema(),
            "dataschema" => $this->toSchema()
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function toUIschema(): array
    {
        return [
            "type" => "FormGroupControl",
            "options" => [
                "section" => true,
                "group" => true
            ],
            "elements" => [
                [
                    "type" => "Group",
                    "elements" => [
                        [
                            "type" => "VerticalLayout",
                            "elements" => [
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/reference",
                                ], [
                                    "type" => "FormGroupControl",
                                    "label" => "Datum indienen",
                                    "elements" => [
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/dateFrom",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ], [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/dateTo",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/status",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ],
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/phase",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ],
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/subsidy",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ], [
                                    "type" => "FormGroupControl",
                                    "label" => "Datum laatste update",
                                    "elements" => [
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/dateLastModifiedFrom",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ],
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/dateLastModifiedTo",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ]
                                    ]
                                ], [
                                    "type" => "FormGroupControl",
                                    "label" => "Uiterste behandeldatum",
                                    "elements" => [
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/dateFinalReviewDeadlineFrom",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ],
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/dateFinalReviewDeadlineTo",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private function toSchema(): array
    {
        //TODO dynamic content
        return [
            "properties" => [
                'reference' => [
                    'type' => 'string',
                    'title' => 'Dossiernummer'
                ],
                'dateFrom' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Van'
                ],
                'dateTo' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Tot'
                ],
                'status' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'oneOf' => [
                            [
                                'const' => ApplicationStatus::Pending,
                                'title'  => 'Nieuw'
                            ],
                            [
                                'const' => ApplicationStatus::RequestForChanges,
                                'title' => 'Aanvulling nodig',
                            ],
                            [
                                'const' => ApplicationStatus::Approved,
                                'title' => 'Goedgekeurd',
                            ],
                            [
                                'const' => ApplicationStatus::Rejected,
                                'title' => 'Afgekeurd',
                            ],
                        ],
                    ],
                    'title' => 'Status'
                ],
                'phase' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'enum' => $this['phases']
                    ],
                    'title' => 'Fase'
                ],
                'subsidy' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'enum' => $this['shortRegulations']
                    ],
                    'title' => 'Regeling'
                ],
                'dateLastModifiedFrom' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Van'
                ],
                'dateLastModifiedTo' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Tot'
                ],
                'dateFinalReviewDeadlineFrom' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Van'
                ],
                'dateFinalReviewDeadlineTo' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Tot'
                ]
            ]
        ];
    }
}
