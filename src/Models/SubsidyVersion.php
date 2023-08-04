<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyVersionFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

/**
 * @property string $id
 * @property string $subsidy_id
 * @property string $title
 * @property string $description
 * @property string $subsidy_page_url
 * @property DateTimeInterface $valid_from
 * @property DateTimeInterface $valid_to
 * @property SubsidyStage[] $forms
 */

class SubsidyVersion extends Model
{
    use HasUuids;
    use HasFactory;


    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    public const UPDATED_AT = null;

    protected $fillable = [
        'version',
        'status',
        'subsidy_page_url',
    ];

    protected $casts = [
        'id' => 'string',
        'status' => VersionStatus::class
    ];

    public function subsidy(): BelongsTo
    {
        return $this->belongsTo(Subsidy::class, 'subsidy_id', 'id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
    //@phpstan-ignore-next-line
        return $query->orderBy('created_at');
    }

    public function subsidyStages(): HasMany
    {
        return $this->hasMany(SubsidyStage::class, 'subsidy_version_id', 'id');
    }

    public function scopeOpen(Builder $query): Builder
    {
    //@phpstan-ignore-next-line
        return $query->whereIn('status', [VersionStatus::Published, VersionStatus::Archived]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeSubjectRole(Builder $query, SubjectRole $role): Builder
    {
        return $query->whereRelation('subsidyStages', fn (Builder $subQuery) => $subQuery->subjectRole($role));
    }

    protected static function newFactory(): SubsidyVersionFactory
    {
        return new SubsidyVersionFactory();
    }
}
