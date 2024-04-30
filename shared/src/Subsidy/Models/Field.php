<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\FieldFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;

/**
 * @property string $id
 * @property string $form_id
 * @property string $code
 * @property string $title
 * @property string $description
 * @property FieldType $type
 * @property array $params
 * @property bool $is_required
 * @property Condition|null $required_condition
 * @property string $source
 * @property DataRetentionPeriod $retention_period_on_approval
 * @property SubsidyStage $subsidyStage
 * @property bool $exclude_from_clone_data
 */
class Field extends Model
{
    use HasUuids;
    use HasFactory;

    /**
     * @var string|null
     */
    protected $connection = Connection::APPLICATION;

    public $timestamps = false;

    protected $casts = [
        'id' => 'string',
        'type' => FieldType::class,
        'source' => FieldSource::class,
        'params' => 'array',
        'is_required' => 'boolean',
        'required_condition' => Condition::class,
        'retention_period_on_approval' => DataRetentionPeriod::class,
    ];

    protected $fillable = [
        'title',
        'description',
        'type',
        'params',
        'is_required',
        'code',
        'source',
        'retention_period_on_approval',
    ];

    public function subsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'subsidy_stage_id', 'id');
    }

    public function subsidyStageHashFields(): HasMany
    {
        return $this->HasMany(SubsidyStageHashField::class, 'field_id', 'id');
    }

    public function scopeWithRetentionPeriod(Builder $query, DataRetentionPeriod $retentionPeriod): Builder
    {
        return $query->where('retention_period_on_approval', '=', $retentionPeriod->value);
    }

    protected static function newFactory(): FieldFactory
    {
        return new FieldFactory();
    }
}
