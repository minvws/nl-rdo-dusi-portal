<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

/**
 * @property string $id
 * @property SubsidyStageTransition $subsidyStageTransition
 * @property ApplicationStage $previousApplicationStage
 * @property ApplicationStage $newApplicationStage
 * @property ApplicationStatus $previous_application_status
 * @property ApplicationStatus $new_application_status
 * @property DateTime $created_at
 */
class ApplicationStageTransitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        $faker = Factory::create();
        return [
            'id' => $this->id,
            'description' => $faker->title,
            'previousApplicationStage' => [
                'subsidyStage' => $this->previousApplicationStage->subsidyStage?->only(['id', 'title', 'stage']),
                'assessorUser' => $this->previousApplicationStage->assessorUser?->only(['id', 'name']),
            ],
            'newApplicationStage' => [
                'subsidyStage' => $this->newApplicationStage->subsidyStage?->only(['id', 'title', 'stage']),
                'assessorUser' => $this->newApplicationStage->assessorUser?->only(['id', 'name']),
            ],
            'message' => [
                'id' => $faker->uuid,
                'subject' => sprintf('Bericht %s', $faker->title),
            ],
            'internalNote' => $faker->text,
            'createdAt' => $this->created_at,
        ];
    }
}
