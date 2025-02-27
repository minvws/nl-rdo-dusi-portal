<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationStageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStageDecision;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @property string $id
 * @property string $application_id
 * @property string $subsidy_stage_id
 * @property int $sequence_number
 * @property boolean $is_current
 * @property string|null $assessor_user_id
 * @property ApplicationStageDecision|null $assessor_decision
 * @property HsmEncryptedData $encrypted_key
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property ?DateTime $expires_at
 * @property bool $is_submitted
 * @property ?DateTime $submitted_at
 * @property ?DateTime $closed_at
 * @property-read Application $application
 * @property-read SubsidyStage $subsidyStage
 * @property-read Collection<array-key, Answer> $answers
 * @property-read Collection<array-key, Field> $fields
 * @property-read ?User $assessorUser
 */
class ApplicationStage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'assessor_decision' => ApplicationStageDecision::class,
        'encrypted_key' => HsmEncryptedData::class,
        'is_submitted' => 'bool',
        'submitted_at' => 'datetime',
        'closed_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    protected $fillable = [
        'submitted_at',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function assessorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_user_id', 'id');
    }

    public function subsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'subsidy_stage_id', 'id');
    }

    public function fields(): HasManyThrough
    {
        return $this->hasManyThrough(
            Field::class,
            SubsidyStage::class,
            'id',
            'subsidy_stage_id',
            'subsidy_stage_id',
            'id',
        );
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'application_stage_id', 'id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sequence_number');
    }

    public function scopeAssessor(Builder $query, User $assessor): Builder
    {
        return $query->where('assessor_user_id', $assessor->id);
    }

    public function scopeSubmittedAtBetween(Builder $query, DateTime $start = null, DateTime $end = null): Builder
    {
        return $query->where(fn (Builder $query) => $query
            ->when($start !== null, fn (Builder $query) => $query->where('submitted_at', '>=', $start))
            ->when($end !== null, fn (Builder $query) => $query->where('submitted_at', '<=', $end)));
    }

    protected static function newFactory(): ApplicationStageFactory
    {
        return ApplicationStageFactory::new();
    }
}
