<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubsidyStage extends Model
{
    use HasFactory;
    use HasUuids;

    public const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'timestamp',
        'final_review_deadline' => 'timestamp',
    ];

    protected $fillable = [
        'title',
        'subject_role',
        'subject_organisation',
        'stage',
        'final_review_deadline',
        'final_review_time_in_s_after_submission',
        'created_at',
    ];

    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class, 'subsidy_version_id', 'id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'subsidy_stage_id', 'id');
    }
}
