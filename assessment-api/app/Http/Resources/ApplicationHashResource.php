<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationHashResource extends JsonResource
{
    public function __construct(string $hash, int $count, string $applicationReferences)
    {
        parent::__construct([
            'hash' => $hash,
            'count' => $count,
            'applicationReferences' => $applicationReferences,
        ]);
    }

    public function toArray(Request $request): array
    {
        return [
            'hash' => $this['hash'],
            'count' => $this['count'],
            'application_references' => explode(',', $this['applicationReferences']),
        ];
    }
}
