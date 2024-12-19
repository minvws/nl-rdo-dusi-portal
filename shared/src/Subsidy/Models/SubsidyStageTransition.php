<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageTransitionFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\ReviewDeadlineSource;

/**
 * @property string $id
 * @property string $description
 * @property string $current_subsidy_stage_id
 * @property-read SubsidyStage $currentSubsidyStage
 * @property string|null $target_subsidy_stage_id
 * @property-read SubsidyStage|null $targetSubsidyStage
 * @property ApplicationStatus|null $target_application_status
 * @property Condition|null $condition
 * @property bool $send_message
 * @property bool $clone_data
 * @property bool $assign_to_previous_assessor
 * @property EvaluationTrigger $evaluation_trigger
 * @property ?int $expiration_period
 * @property ReviewDeadlineSource $target_application_review_deadline_source
 * @property ?FieldReference $target_application_review_deadline_source_field
 * @property ?string $target_application_review_deadline_additional_period
 * @property-read Collection<int, SubsidyStageTransitionMessage> $subsidyStageTransitionMessages
 * @property-read SubsidyStageTransitionMessage|null $publishedSubsidyStageTransitionMessage
 */
class SubsidyStageTransition extends Model
{
    use HasUuids;
    use HasFactory;

    protected $connection = Connection::APPLICATION;
    public $timestamps = false;

    protected $casts = [
        'id' => 'string',
        'target_application_status' => ApplicationStatus::class,
        'condition' => Condition::class,
        'send_letter' => 'bool',
        'assign_to_previous_assessor' => 'bool',
        'evaluation_trigger' => EvaluationTrigger::class,
        'target_application_review_deadline_source' => ReviewDeadlineSource::class,
        'target_application_review_deadline_source_field' => FieldReference::class,
    ];

    public function currentSubsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'current_subsidy_stage_id', 'id');
    }

    public function targetSubsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'target_subsidy_stage_id', 'id');
    }

    public function subsidyStageTransitionMessages(): HasMany
    {
        return $this->hasMany(SubsidyStageTransitionMessage::class, 'subsidy_stage_transition_id', 'id');
    }

    public function publishedSubsidyStageTransitionMessage(): HasOne
    {
        return $this->hasOne(SubsidyStageTransitionMessage::class)->published();
    }

    protected static function newFactory(): SubsidyStageTransitionFactory
    {
        return new SubsidyStageTransitionFactory();
    }
}
