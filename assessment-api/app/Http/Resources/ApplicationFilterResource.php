<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;
use DateTime;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\User\Models\User;

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
        $user = $request->user();
        assert($user instanceof User);

        $application = $this->resource;
        assert($application instanceof Application);

        $actions = [];
        foreach (['show', 'claim', 'release', 'assign'] as $action) {
            if ($user->can($action, [$application])) {
                $actions[] = $action;
            }
        }

        $result = [
            'id' => $this->id,
            'reference' => $this->reference,
            'subsidy_page_url' => $this->subsidyVersion->subsidy_page_url,
            'application_title' => $this->application_title,
            'subsidy' => $this->subsidyVersion->subsidy->code,
            'status' => $this->status->value,
            'subsidy_stage_title' => $this->currentApplicationStage->subsidyStage->title ?? 'Afgerond',
            'final_review_deadline' => $this->final_review_deadline,
            'updated_at' => $this->updated_at,
            'assessor' => null,
            'actions' => $actions
        ];

        if (
            $user->can('viewAllStagesAndAssessor', [Application::class, $application->subsidyVersion->subsidy]) &&
            isset($this->currentApplicationStage->assessorUser)
        ) {
            $result['assessor'] = [
                'id' => $this->currentApplicationStage->assessorUser->id,
                'name' => $this->currentApplicationStage->assessorUser->name
            ];
        }

        return $result;
    }
}
