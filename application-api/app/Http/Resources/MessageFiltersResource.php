<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageFiltersResource extends JsonResource
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            "uischema" => $this->toUIschema(),
            "schema" => $this->toSchema()
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
                                    "type" => "FormGroupControl",
                                    "label" => "Datum",
                                    "elements" => [
                                        [
                                            "type" => "CustomControl",
                                            "scope" => "#/properties/dateFrom",
                                            "options" => [
                                                "inline" => true
                                            ]
                                        ],
                                        [
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
                                    "scope" => "#/properties/subsidies",
                                    "options" => [
                                        "format" => "checkbox-group"
                                    ]
                                ],
                                [
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/status",
                                    "options" => [
                                        "format" => "checkbox-group"
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
        return [
            "properties" => [
                "dateFrom" => [
                    "type" => "string",
                    "format" => "date",
                    "title" => "Van"
                ],
                "dateTo" => [
                    "type" => "string",
                    "format" => "date",
                    "title" => "Tot"
                ],
                "subsidies" => [
                    "type" => "array",
                    "items" => [
                        "type" => "string",
                        // TODO: Wait for data struct
                        "enum" => ['ontvangen', 'goedgekeurd', 'afgekeurd', 'ter aanvulling'],
                    ],
                    "title" => "Regeling"
                ],
                "status" => [
                    "type" => "array",
                    "items" => [
                        "type" => "string",
                        "enum" => $this['shortRegulations']
                    ],
                    "title" => "Brieftype"
                ]
            ]
        ];
    }
}
