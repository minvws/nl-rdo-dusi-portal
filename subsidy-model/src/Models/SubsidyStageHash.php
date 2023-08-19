<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubsidyStageHash extends Model
{
    use HasUuids;
    use HasFactory;

    protected $connection = Connection::FORM;

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
}
