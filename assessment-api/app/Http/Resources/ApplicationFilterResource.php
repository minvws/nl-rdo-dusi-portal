<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;
use DateTime;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

/**
 * @property string $id
 * @property ApplicationStatus $status
 * @property string $reference
 * @property string $subsidy_version_id
 * @property string $application_title
 * @property DateTime $updated_at
 * @property DateTime $final_review_deadline
 * @property SubsidyVersion $subsidyVersion
 * @property ApplicationStage|null $currentApplicationStage
 * @property Collection<ApplicationStage> $applicationStages
 */
class ApplicationFilterResource extends JsonResource
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
            'reference' => $this->reference,
            'external_subsidy_id' => $this->subsidyVersion->subsidy_page_url,
            'application_title' => $this->application_title,
            'subsidy' => $this->subsidyVersion->subsidy->code,
            'status' => $this->status->value,
            'fase' => $this->currentApplicationStage->subsidyStage->title ?? 'Afgerond',
            'final_review_deadline' => $this->final_review_deadline,
            'updated_at' => $this->updated_at,
        ];
    }
}
