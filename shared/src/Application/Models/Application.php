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
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationFactory;
use MinVWS\DUSi\Shared\Application\Eloquent\HasOneUuidSupport;
use MinVWS\DUSi\Shared\Application\Traits\ApplicationScopes;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

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
 * @property DateTimeInterface $updated_at
 * @property-read DateTimeInterface|null $submitted_at
 * @property-read bool $is_editable_for_applicant
 * @property-read SubsidyVersion $subsidyVersion
 * @property-read Collection<ApplicationMessage> $applicationMessages
 * @property-read ApplicationStage|null $currentApplicationStage
 * @property-read ApplicationStage $lastApplicationStage
 * @property-read ApplicationStage $firstApplicationStage
 * @property-read Collection<string, ApplicationStage> $applicationStages
 * @property-read Collection<ApplicationStageTransition> $applicationStageTransitions
 * @property-read Collection<ApplicationHash> $applicationHashes
 * @property-read ApplicationSurePayResult|null $applicationSurePayResult
 * @method static Builder<self> forIdentity(Identity $identity)
 * @method Builder<self> forIdentity(Identity $identity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Application extends Model
{
    use HasFactory;
    use HasUuids;
    use ApplicationScopes;

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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) - $value is not used
     */
    protected function submittedAt(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attrs) =>
                ApplicationStage::query()
                    ->where('application_id', '=', $attrs['id'])
                    ->whereRelation('subsidyStage', 'stage', '=', 1)
                    ->whereRelation('subsidyStage', 'subject_role', '=', SubjectRole::Applicant)
                    ->where('is_submitted', '=', true)
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

    public function subsidy(): HasOneThrough
    {
        return $this->hasOneThrough(
            related: Subsidy::class,
            through: SubsidyVersion::class,
            firstKey: 'id',
            secondKey: 'id',
            localKey: 'subsidy_version_id',
            secondLocalKey: 'subsidy_id',
        );
    }

    /**
     * @return HasOne<ApplicationStage>
     */
    public function currentApplicationStage(): HasOne
    {
        return
            $this->hasOne(ApplicationStage::class)
                ->ofMany([
                    'sequence_number' => 'MAX',
                    'id' => 'MAX',
                ], function (Builder $query) {
                    $query->where('is_current', true);
                });
    }

    /**
     * @return HasOne<ApplicationStage>
     */
    public function lastApplicationStage(): HasOne
    {
        return
            $this->hasOne(ApplicationStage::class)
                ->ofMany('sequence_number', 'MAX');
    }

    /**
     * @return HasOne<ApplicationStage>
     */
    public function firstApplicationStage(): HasOne
    {
        return
            $this->hasOne(ApplicationStage::class)
                ->ofMany('sequence_number', 'MIN');
    }

    /**
     * @return BelongsTo<Identity, Application>
     */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class, 'identity_id', 'id');
    }

    /**
     * @psalm-return BelongsTo
     * @return BelongsTo<SubsidyVersion, Application>
     */
    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class, 'subsidy_version_id', 'id');
    }

    protected static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory();
    }

    /**
     * Override the hasOne method to use the custom HasOneUuidSupport class
     * to support UUIDs in the hasOne -> ofMany functionality.
     */
    protected function newHasOne(Builder $query, Model $parent, $foreignKey, $localKey): HasOne
    {
        return new HasOneUuidSupport($query, $parent, $foreignKey, $localKey);
    }
}
