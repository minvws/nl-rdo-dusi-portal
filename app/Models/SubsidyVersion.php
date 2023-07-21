<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubsidyVersion extends Model
{
    use HasFactory;
    use HasUuids;

    public const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'timestamp',
    ];
    protected $fillable = [
        'id',
        'created_at',
        'subsidy_id',
        'version',
        'status',
    ];

    public function subsidy(): BelongsTo
    {
        return $this->belongsTo(Subsidy::class, 'subsidy_id', 'id');
    }

    public function subsidyStages(): HasMany
    {
        return $this->hasMany(SubsidyStage::class, 'subsidy_version_id', 'id');
    }
}
