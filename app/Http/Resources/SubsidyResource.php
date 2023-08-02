<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;

class SubsidyResource extends JsonResource
{
    /**
     * @var \MinVWS\DUSi\Shared\Subsidy\Models\Subsidy
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'validFrom' => $this->valid_from->format('Y-m-d'),
            'validTo' => $this->valid_to?->format('Y-m-d')
        ];

        $applicantStage = $this->publishedVersion->subsidyStages->filter(
            fn ($stage) => $stage->subject_role === SubjectRole::Applicant
        )->first();

        if (!isset($applicantStage)) {
            return $data;
        }

        $data['publishedForm'] = [
            'id' => $applicantStage->id,
            'version' => $this->publishedVersion->version
        ];

        $data['_links'] = [
            'form' => ['href' => route('api.form-show', ['form' => $applicantStage->id]), false]
        ];

        return $data;
    }
}
