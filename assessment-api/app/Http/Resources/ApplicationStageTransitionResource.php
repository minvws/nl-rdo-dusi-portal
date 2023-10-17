<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use DateTimeInterface;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

/**
 * @property string                      $id
 * @property string                      $description
 * @property string                      $application_id
 * @property DateTimeInterface           $created_at
 * @property-read Application            $application
 * @property-read SubsidyStageTransition $subsidyStageTransition
 * @property-read ApplicationStage       $previousApplicationStage
 * @property-read ?ApplicationStage      $newApplicationStage
 * @property-read ApplicationStatus      $previous_application_status
 * @property-read ApplicationStatus      $new_application_status
 * @property-read ?ApplicationMessage    $applicationMessage
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
        $data = [
            'id' => $this->id,
            'internalNote' => $faker->text,
            'createdAt' => $this->created_at,
            'previousApplicationStatus' => $this->previous_application_status,
            'newApplicationStatus' => $this->new_application_status,
            'subsidyStageTransition' => $this->subsidyStageTransition->only(['id', 'description']),
            'previousApplicationStage' => [
                'subsidyStage' => $this->previousApplicationStage->subsidyStage?->only(['id', 'title', 'stage']),
                'assessorUser' => $this->previousApplicationStage->assessorUser?->only(['id', 'name']),
            ],
        ];

        if ($this->newApplicationStage) {
            $data['newApplicationStage'] = [
                'subsidyStage' => $this->newApplicationStage->subsidyStage?->only(['id', 'title', 'stage']),
                'assessorUser' => $this->newApplicationStage->assessorUser?->only(['id', 'name']),
            ];
        }

        if ($this->applicationMessage) {
            $data['message'] = [
                'id' => $this->applicationMessage->id,
                'subject' => $this->applicationMessage->subject,
            ];
        }

        return $data;
    }
}
