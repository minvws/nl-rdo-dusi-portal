<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationStageTransitionFactory;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;

/**
 * @property string $id
 * @property string $application_id
 * @property string $subsidy_stage_transition_id
 * @property string $previous_application_stage_id
 * @property ?string $new_application_stage_id
 * @property DateTimeInterface $created_at
 * @property-read Application $application
 * @property-read SubsidyStageTransition $subsidyStageTransition
 * @property-read ApplicationStage $previousApplicationStage
 * @property-read ?ApplicationStage $newApplicationStage
 * @property-read ApplicationStatus $previousApplicationStatus
 * @property-read ApplicationStatus $newApplicationStatus
 */
class ApplicationStageTransition extends Model
{
    use HasFactory;
    use HasUuids;

    public const UPDATED_AT = null; // disable updated_at timestamp

    protected $casts = [
        'previous_application_status' => ApplicationStatus::class,
        'new_application_status' => ApplicationStatus::class
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function subsidyStageTransition(): BelongsTo
    {
        return $this->belongsTo(SubsidyStageTransition::class, 'subsidy_stage_transition_id', 'id');
    }

    public function previousApplicationStage(): BelongsTo
    {
        return $this->belongsTo(ApplicationStage::class, 'previous_application_stage_id', 'id');
    }

    public function newApplicationStage(): BelongsTo
    {
        return $this->belongsTo(ApplicationStage::class, 'new_application_stage_id', 'id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('created_at');
    }

    protected static function newFactory(): ApplicationStageTransitionFactory
    {
        return ApplicationStageTransitionFactory::new();
    }
}
