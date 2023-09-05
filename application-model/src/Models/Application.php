<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationFactory;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

/**
 * @property string $id
 * @property string $subsidy_version_id
 * @property string $application_title
 * @property string $identity_type
 * @property string $identity_identifier
 * @property Identity $identity
 * @property DateTime $locked_from
 * @property DateTime $final_review_deadline
 * @property DateTime $created_at
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'identity_type' => IdentityType::class,
        'locked_from' => 'datetime',
        'final_review_deadline' => 'datetime',
    ];

    protected $fillable = [
        'subsidy_version_id',
        'application_title',
        'final_review_deadline',
        'locked_from'
    ];

    public function applicationHashes(): HasMany
    {
        return $this->hasMany(ApplicationHash::class, 'application_id', 'id');
    }

    public function applicationStages(): HasMany
    {
        return $this->hasMany(ApplicationStage::class, 'application_id', 'id');
    }

    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class, 'subsidy_version_id', 'id');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function identity(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => new Identity(
                IdentityType::from($attributes['identity_type']),
                $attributes['identity_identifier']
            ),
            set: fn (Identity $identity) => [
                'identity_type' => $identity->type,
                'identity_identifier' => $identity->identifier
            ]
        );
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('application_title', 'LIKE', '%' . $title . '%');
    }

    public function scopeCreatedAtFrom(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('created_at', '>=', $timestamp);
    }

    public function scopeCreatedAtTo(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('created_at', '<=', $timestamp);
    }

    public function scopeUpdatedAtFrom(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('updated_at', '>=', $timestamp);
    }

    public function scopeUpdatedAtTo(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('updated_at', '<=', $timestamp);
    }

    public function scopeFinalReviewDeadlineFrom(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('final_review_deadline', '>=', $timestamp);
    }

    public function scopeFinalReviewDeadlineTo(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('final_review_deadline', '<=', $timestamp);
    }

    public function scopeStatus(Builder $query, ApplicationStageVersionStatus $status): Builder
    {
        return $query->whereHas('applicationStages.applicationStageVersions', function (Builder $query) use ($status) {
            $query->where('status', $status);
        });
    }

    //TODO GB: This is not the correct way to do this, but it works for now
    public function scopeSubsidyTitle(Builder $query, string $title): Builder
    {
        $subVersions = SubsidyVersion::query()->whereHas('subsidy', function ($q) use ($title) {
            $q->where('title', $title);
        })->pluck('id');

        return $query->whereIn('subsidy_version_id', $subVersions);
    }

    protected static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory();
    }
}
