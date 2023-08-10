<?php

declare(strict_types=1);

namespace App\Http\Resources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $subsidy_version_id
 * @property mixed $application_title
 * @property mixed $updated_at
 * @property mixed $final_review_deadline
 * @property mixed $applicationStages
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
        $subsidyTitle = DB::connection('pgsql_form')
            ->table('subsidy_versions')
            ->join('subsidies', 'subsidies.id', '=', 'subsidy_versions.subsidy_id')
            ->where('subsidy_versions.id', $this->subsidy_version_id)
            ->value('subsidies.title');

        return [
            'id' => $this->id,
            'application_title' => $this->application_title,
            'subsidy' => $subsidyTitle,
            'status' => $this->applicationStages->last()->applicationStageVersions->last()->status,
            'final_review_deadline' => $this->final_review_deadline,
            'updated_at' => $this->updated_at,
        ];
    }
}
