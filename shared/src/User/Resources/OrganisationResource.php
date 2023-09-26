<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\User\Models\Organisation;

/**
 * @mixin Organisation
 */
class OrganisationResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
