<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationFactory;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
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
 * @property-read DateTimeInterface|null $submitted_at
 * @property-read bool $is_editable_for_applicant
 * @property-read SubsidyVersion $subsidyVersion
 * @property-read Collection<ApplicationMessage> $applicationMessages
 * @property-read ApplicationStage|null $currentApplicationStage
 * @property-read ApplicationStage $lastApplicationStage
 * @property-read Collection<string, ApplicationStage> $applicationStages
 * @property-read Collection<ApplicationStageTransition> $applicationStageTransitions
 * @property-read ApplicationSurePayResult|null $applicationSurePayResult
 * @method static Builder<self> forIdentity(Identity $identity)
 * @method Builder<self> forIdentity(Identity $identity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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

    protected function submittedAt(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attrs) =>
                ApplicationStage::query()
                    ->where('application_id', '=', $attrs['id'])
                    ->whereRelation('subsidyStage', 'stage', '=', 1)
                    ->whereRelation('subsidyStage', 'subject_role', '=', SubjectRole::Applicant)
                    ->orderBy('sequence_number')
                    ->limit(1)
                    ->first(['submitted_at'])
                    ?->submitted_at
        )->shouldCache();
    }

    protected function isEditableForApplicant(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                $this->status->isEditableForApplicant() &&
                (
                    $this->status->isEditableForApplicantAfterClosure() ||
                    $this->subsidyVersion->subsidy->is_open_for_new_applications
                )
        );
    }

    public function applicationSurePayResult(): HasOne
    {
        return $this->hasOne(ApplicationSurePayResult::class, 'application_id', 'id');
    }

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

    public function applicationStageTransitions(): HasMany
    {
        return $this->hasMany(ApplicationStageTransition::class, 'application_id', 'id')
            ->oldest();
    }

    public function currentApplicationStage(): HasOne
    {
        return
            $this->hasOne(ApplicationStage::class)
                ->where('is_current', true)
                ->orderBy('sequence_number', 'desc')
                ->limit(1);
    }

    public function lastApplicationStage(): HasOne
    {
        return
            $this->hasOne(ApplicationStage::class)
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
    public function scopeReference(Builder $query, string $title): Builder
    {
        return $query->where('reference', 'LIKE', '%' . $title . '%');
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

    public function scopeStatus(Builder $query, array $status): Builder
    {
        return $query->whereIn('status', $status);
    }

    public function scopeSubsidyCode(Builder $query, array $codes): Builder
    {
        return $query->whereHas('subsidyVersion.subsidy', function (Builder $q) use ($codes) {
            $q->whereIn('code', $codes);
        });
    }

    /**
     * Note that we search explicitly for a stage title as multiple subsidy stages belonging to different susbsidies
     * could have the same stage title
     */
    public function scopePhase(Builder $query, array $titles): Builder
    {
        return $query->whereHas('currentApplicationStage.subsidyStage', function (Builder $q) use ($titles) {
            $q->whereIn('title', $titles);
        });
    }

    protected static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory();
    }
}
