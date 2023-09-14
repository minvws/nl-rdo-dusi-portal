<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

/**
 * @property string $id
 * @property string $subsidy_id
 * @property int $version
 * @property string $title
 * @property VersionStatus $status
 * @property int $stage
 * @property SubjectRole $subject_role
 * @property-read SubsidyVersion $subsidyVersion
 */
class SubsidyStage extends Model
{
    use HasUuids;
    use HasFactory;

    protected $connection = Connection::APPLICATION;

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
        'id' => 'string',
        'subject_role' => SubjectRole::class,
        'final_review_deadline' => 'timestamp',
    ];

    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class, 'subsidy_version_id', 'id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'subsidy_stage_id', 'id');
    }

    public function uis(): HasMany
    {
        return $this->hasMany(SubsidyStageUI::class, 'subsidy_stage_id', 'id');
    }

    public function publishedUI(): HasOne
    {
        return $this->hasOne(SubsidyStageUI::class)->where('status', '=', 'published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('stage');
    }

    public function scopeSubjectRole(Builder $query, SubjectRole $subjectRole): Builder
    {
        return $query->whereIn('subject_role', [$subjectRole]);
    }

    protected static function newFactory(): SubsidyStageFactory
    {
        return new SubsidyStageFactory();
    }
}
