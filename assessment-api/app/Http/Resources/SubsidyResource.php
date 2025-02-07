<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

/**
 * @mixin Subsidy
 */
class SubsidyResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array{id: string, title: string, description: string, validFrom: string, validTo: string|null}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'validFrom' => $this->valid_from->format('Y-m-d\TH:i:sp'),
            'validTo' => $this->valid_to?->format('Y-m-d\TH:i:sp'),
        ];
    }
}
