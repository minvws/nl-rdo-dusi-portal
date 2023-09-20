<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;
use DateTime;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

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
     * @param array<string> $regulations
     */
    //TODO dynamic regulations
    public function __construct(array $regulations)
    {
        parent::__construct($regulations);
    }

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
                                    "scope" => "#/properties/caseNumber",
                                ], [
                                    "type" => "FormGroupControl",
                                    "label" => "Datum indeling",
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
                                ], [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/status",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ], [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/assessment",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ], [
                                    "type" => "FormGroupControl",
                                    "label" => "Datum laatste update",
                                    "elements" => [
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/updateFrom",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ],
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/updateTo",
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
                                            "scope" => "#/properties/treatementFrom",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ],
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/treatementTo",
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
                'caseNumber' => [
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
                        'enum' => [
                            'Nieuw',
                            'Eerste beoordeling afgerond',
                            'Tweede beoordeling afgerond',
                            'Goedgekeurd',
                            'Afbekeurd',
                            'Herbeoordeling nodig'
                        ]
                    ],
                    'title' => 'Status'
                ],
                'assessment' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'enum' => $this['shortRegulations']
                    ],
                    'title' => 'Regeling'
                ],
                'updateFrom' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Van'
                ],
                'updateTo' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Tot'
                ],
                'treatementFrom' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Van'
                ],
                'treatementTo' => [
                    'type' => 'string',
                    'format' => 'date',
                    'title' => 'Tot'
                ]
            ]
        ];
    }
}
