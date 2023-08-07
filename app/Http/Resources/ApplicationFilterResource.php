<?php

namespace App\Http\Resources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subsidyTitle = DB::connection('pgsql_form')
            ->table('subsidy_versions')
            ->join('subsidies', 'subsidies.id', '=', 'subsidy_versions.subsidy_id')
            ->where('subsidy_versions.id', $this->subsidy_version_id)
            ->value('subsidies.title');

        return [
            'application_title' => $this->application_title,
            'subsidy' => $subsidyTitle,
            'status' => $this->applicationStages->last()->applicationStageVersions->last()->status,
            'final_review_deadline' => $this->final_review_deadline,
            'updated_at' => $this->updated_at,
        ];
    }
}
