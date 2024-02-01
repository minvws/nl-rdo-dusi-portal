<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationMessageFilterResource extends JsonResource
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
                                    "type" => "CustomControl",
                                    "scope" => "#/properties/search"
                                ],
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
                                    "scope" => "#/properties/assessment",
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
                "search" => [
                    "type" => "string",
                    "title" => "Zoeken"
                ],
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
                "assessment" => [
                    "type" => "array",
                    "items" => [
                        "type" => "string",
                        "enum" => $this['shortRegulations']
                    ],
                    "title" => "Regeling"
                ]
            ]
        ];
    }
}
