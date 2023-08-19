<?php

declare(strict_types=1);

namespace App\Http\Resources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;
use DateTime;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;

/**
 * @property string $id
 * @property string $subsidy_version_id
 * @property string $application_title
 * @property DateTime $updated_at
 * @property DateTime $final_review_deadline
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
        $subsidyTitle = DB::connection('pgsql_form')
            ->table('subsidy_versions')
            ->join('subsidies', 'subsidies.id', '=', 'subsidy_versions.subsidy_id')
            ->where('subsidy_versions.id', $this->subsidy_version_id)
            ->value('subsidies.title');

        return [
            'id' => $this->id,
            'external_subsidy_id' => "ToDo",
            'application_title' => $this->application_title,
            'subsidy' => $subsidyTitle,
            'status' => "ToDo",
            'final_review_deadline' => $this->final_review_deadline,
            'updated_at' => $this->updated_at,
        ];
    }
}
