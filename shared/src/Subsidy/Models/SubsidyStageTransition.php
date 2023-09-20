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

/**
 * @property string $id
 * @property string $current_subsidy_stage_id
 * @property-read SubsidyStage $currentSubsidyStage
 * @property string|null $target_subsidy_stage_id
 * @property-read SubsidyStage|null $targetSubsidyStage
 * @property ApplicationStatus|null $target_application_status
 * @property Condition|null $condition
 * @property bool $send_message
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
        'send_letter' => 'bool'
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
