<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationFactory;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

/**
 * @property string $id
 * @property ApplicationStatus $status
 * @property string $reference
 * @property string $subsidy_version_id
 * @property string $application_title
 * @property string $identity_id
 * @property Identity $identity
 * @property DateTime $locked_from
 * @property DateTimeInterface|null $final_review_deadline
 * @property DateTimeInterface $created_at
 * @property-read SubsidyVersion $subsidyVersion
 * @property-read HasMany<ApplicationMessage> $applicationMessages
 * @property-read ApplicationStage $currentApplicationStage
 * @method static Builder<self> forIdentity(Identity $identity)
 * @method Builder<self> forIdentity(Identity $identity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'status' => ApplicationStatus::class,
        'locked_from' => 'datetime',
        'final_review_deadline' => 'datetime',
    ];

    protected $fillable = [
        'subsidy_version_id',
        'reference',
        'application_title',
        'final_review_deadline',
        'locked_from'
    ];

    public function applicationHashes(): HasMany
    {
        return $this->hasMany(ApplicationHash::class, 'application_id', 'id');
    }

    public function applicationMessages(): HasMany
    {
        return $this->hasMany(ApplicationMessage::class, 'application_id', 'id');
    }

    public function applicationStages(): HasMany
    {
        return $this->hasMany(ApplicationStage::class, 'application_id', 'id');
    }

    public function currentApplicationStage(): HasOne
    {
        return
            $this->hasOne(ApplicationStage::class)
                ->where('is_current', true)
                ->orderBy('sequence_number', 'desc')
                ->limit(1);
    }

    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class, 'identity_id', 'id');
    }

    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class, 'subsidy_version_id', 'id');
    }

    public function scopeForIdentity(Builder $query, Identity $identity): Builder
    {
        return $query->where('identity_id', $identity->id);
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

    public function scopeStatus(Builder $query, ApplicationStatus $status): Builder
    {
        return $query->where('status', $status);
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
