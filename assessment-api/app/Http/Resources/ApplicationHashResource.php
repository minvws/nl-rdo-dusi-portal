<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property string $hash
 * @property int $count
 * @property Collection $applications
 */
class ApplicationHashResource extends JsonResource
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            'hash' => $this->hash,
            'count' => $this->count,
            'applications' => ApplicationResource::collection($this->applications),
        ];
    }
}
