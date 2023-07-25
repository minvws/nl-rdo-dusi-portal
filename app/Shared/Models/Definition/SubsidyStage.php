<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition;

use App\Shared\Models\Definition\Enums\SubjectRole;
use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Factories\SubsidyStageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $title
 */
class SubsidyStage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::FORM;

    public const UPDATED_AT = null;

    protected $fillable = [
        'title',
        'subject_role',
        'subject_organisation',
        'stage',
        'final_review_deadline',
        'final_review_time_in_s_after_submission',
    ];

    protected $casts = [
        'subject_role' => SubjectRole::class,
        'final_review_deadline' => 'timestamp',
    ];

    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class, 'subsidy_version_id', 'id');
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('stage');
    }

    protected static function newFactory(): SubsidyStageFactory
    {
        return new SubsidyStageFactory();
    }
}
