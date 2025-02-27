<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageHashFactory;

/**
 * @property string $id
 * @property string $subsidy_stage_id
 * @property string $name
 * @property string $description
 */
class SubsidyStageHash extends Model
{
    use HasUuids;
    use HasFactory;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'id' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    protected $fillable = [
        'description',
        'name',
        'created_at',
        'updated_at'
    ];

    public function subsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'subsidy_stage_id', 'id');
    }

    public function subsidyStageHashFields(): HasMany
    {
        return $this->HasMany(SubsidyStageHashField::class, 'subsidy_stage_hash_id', 'id');
    }

    protected static function newFactory(): SubsidyStageHashFactory
    {
        return new SubsidyStageHashFactory();
    }
}
