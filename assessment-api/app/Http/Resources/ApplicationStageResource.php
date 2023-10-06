<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @property string $id
 * @property ?User $assessorUser
 * @property ?SubsidyStage $subsidyStage
 * @property DateTime $created_at
 * @property DateTime $submitted_at
 * @property DateTime $updated_at
 */
class ApplicationStageResource extends JsonResource
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
            'id' => $this->id,
            'subsidyStage' => $this->subsidyStage?->only(['id', 'title', 'stage']),
            'assessorUser' => $this->assessorUser?->only(['id', 'name']),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'submittedAt' => $this->submitted_at,
        ];
    }
}
