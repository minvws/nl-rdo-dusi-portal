<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use MinVWS\DUSi\Assessment\API\Models\Enums\ApplicationFilterType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationCountResource extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @param int $priority
     * @param int $taken
     * @param int $assigned
     * @param int $assignedToMe
     */
    public function __construct(int $priority, int $taken, int $assigned, int $assignedToMe)
    {
        parent::__construct([
            'priority' => $priority,
            'taken' => $taken,
            'assigned' => $assigned,
            'assigned_to_me' => $assignedToMe
        ]);
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
            ApplicationFilterType::Priority->value => $this['priority'],
            ApplicationFilterType::Taken->value => $this['taken'],
            ApplicationFilterType::Assigned->value => $this['assigned'],
            ApplicationFilterType::AssignedToMe->value => $this['assigned_to_me'],
        ];
    }
}
