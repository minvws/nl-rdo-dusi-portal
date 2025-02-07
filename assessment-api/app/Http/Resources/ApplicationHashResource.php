<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationHash;

/**
 * @mixin ApplicationHash
 */
class ApplicationHashResource extends JsonResource
{
    /**
     * @return array{hash: string, count: int, applications: ResourceCollection}
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
