<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

/**
 * @property string $id
 * @property string $title
 * @property string $description
 * @property DateTimeInterface $valid_from
 * @property DateTimeInterface $valid_to
 * @property Collection<SubsidyVersion> $subsidyVersions
 * @property SubsidyVersion $publishedVersion
 * @method static SubsidyVersion|Builder publishedVersion()
 */
class Subsidy extends Model
{
    use HasUuids;
    use HasFactory;

    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    protected $fillable = [
        'title',
        'description',
        'valid_from',
        'valid_to',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'string',
        'status' => VersionStatus::class,
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function subsidyVersions(): HasMany
    {
        return $this->hasMany(SubsidyVersion::class, 'subsidy_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function publishedVersion(): HasOne
    {
        return $this->hasOne(SubsidyVersion::class)->published();
    }
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('title');
    }

    protected static function newFactory(): SubsidyFactory
    {
        return new SubsidyFactory();
    }

    public function scopeActive(Builder $query): Builder
    {
        //@phpstan-ignore-next-line
        return $query->whereRelation('subsidyVersions', fn (Builder $subQuery) => $subQuery->open());
    }

    public function scopeSubjectRole(Builder $query, SubjectRole $role): Builder
    {
        //@phpstan-ignore-next-line
        return $query->whereRelation('subsidyVersions', fn (Builder $subQuery) => $subQuery->subjectRole($role));
    }
}
