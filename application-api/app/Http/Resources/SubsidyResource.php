<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;

/**
 * @property mixed $id
 * @property mixed $title
 * @property mixed $description
 * @property mixed $valid_from
 * @property mixed $valid_to
 * @property mixed $publishedVersion
 */
class SubsidyResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'validFrom' => $this->valid_from->format('Y-m-d'),
            'validTo' => $this->valid_to?->format('Y-m-d'),
            'subsidy_page_url' => $this->publishedVersion->subsidy_page_url,
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
