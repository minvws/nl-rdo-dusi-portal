<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property string $reference_prefix
 * @property string $code
 * @property string $description
 * @property CarbonImmutable $valid_from
 * @property CarbonImmutable|null $valid_to
 * @property boolean $is_open_for_new_applications
 * @property Collection<SubsidyVersion> $subsidyVersions
 * @property SubsidyVersion $publishedVersion
 * @property boolean $allow_multiple_applications
 * @method static SubsidyVersion|Builder publishedVersion()
 */
class Subsidy extends Model
{
    use HasUuids;
    use HasFactory;

    /**
     * @var string|null
     */
    protected $connection = Connection::APPLICATION;

    protected $fillable = [
        'reference_prefix',
        'title',
        'code',
        'description',
        'valid_from',
        'valid_to',
        'created_at',
        'updated_at',
        'short_retention_period',
        'long_retention_period',
    ];

    protected $casts = [
        'id' => 'string',
        'status' => VersionStatus::class,
        'valid_from' => 'immutable_datetime',
        'valid_to' => 'immutable_datetime',
        'short_retention_period' => 'integer',
        'long_retention_period' => 'integer',
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
        return $this->hasOne(SubsidyVersion::class)->orderedByVersion()->published();
    }

    protected static function newFactory(): SubsidyFactory
    {
        return new SubsidyFactory();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('title');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('subsidyVersions', function (Builder $subQuery) {
            /** @var Builder<SubsidyVersion> $subQuery */
            return $subQuery->open();
        });
    }

    public function scopeSubjectRole(Builder $query, SubjectRole $role): Builder
    {
        return $query->whereHas('subsidyVersions', function (Builder $subQuery) use ($role) {
            /** @var Builder<SubsidyVersion> $subQuery */
            return $subQuery->subjectRole($role);
        });
    }

    public function scopeFilterByIds(Builder $query, ?array $subsidyIds = null): Builder
    {
        if ($subsidyIds === null) {
            return $query;
        }

        return $query->whereIn('id', $subsidyIds);
    }

    /**
     * Scope to get only subsidies that are valid today.
     *
     * @param Builder<Subsidy> $query
     * @return Builder<Subsidy>
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->where('valid_from', '<=', CarbonImmutable::today())
            ->where(function (Builder $query) {
                $query
                    ->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', CarbonImmutable::tomorrow());
            });
    }

    protected function isOpenForNewApplications(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->valid_from->isPast() && ($this->valid_to === null || $this->valid_to->isFuture())
        );
    }
}
