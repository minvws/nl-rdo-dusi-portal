<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\User\Enums\Role;

/**
 * @property string $id
 * @property int $version
 * @property string $title
 * @property VersionStatus $status
 * @property int $stage
 * @property SubjectRole $subject_role
 * @property Role|null $assessor_user_role
 * @property string|null $internal_note_field_code
 * @property-read SubsidyVersion $subsidyVersion
 * @property-read Collection<int, SubsidyStageTransition> $subsidyStageTransitions
 * @property-read Collection<int, SubsidyStageHash> $subsidyStagesHashes
 * @property-read Field|null $internalNoteField
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
        'stage'
    ];

    protected $casts = [
        'id' => 'string',
        'subject_role' => SubjectRole::class,
        'assessor_user_role' => Role::class
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

    public function subsidyStageTransitions(): HasMany
    {
        return $this->hasMany(SubsidyStageTransition::class, 'current_subsidy_stage_id', 'id');
    }

    public function subsidyStageHashes(): HasMany
    {
        return $this->hasMany(SubsidyStageHash::class, 'subsidy_stage_id', 'id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('stage');
    }

    public function scopeSubjectRole(Builder $query, SubjectRole $subjectRole): Builder
    {
        return $query->whereIn('subject_role', [$subjectRole]);
    }

    public function scopeBySubsidyIds(Builder $query, ?array $subsidyIds = null): Builder
    {
        if ($subsidyIds === null || count($subsidyIds) === 0) {
            return $query;
        }

        return $query->whereIn('subsidy_version_id', function ($query) use ($subsidyIds) {
            $query->select('id')
                ->from('subsidy_versions')
                ->whereIn('subsidy_id', $subsidyIds);
        });
    }

    public function internalNoteField(): HasOne
    {
        return $this->hasOne(Field::class, 'code', 'internal_note_field_code');
    }

    protected static function newFactory(): SubsidyStageFactory
    {
        return new SubsidyStageFactory();
    }
}
