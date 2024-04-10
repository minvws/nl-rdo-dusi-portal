<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
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
 * @property int $version
 * @property string $created_at
 * @property string $subsidy_id
 * @property string $title
 * @property string $description
 * @property string $subsidy_page_url
 * @property DateTimeInterface $valid_from
 * @property DateTimeInterface $valid_to
 * @property string $contact_mail_address
 * @property string $mail_to_name_field_identifier
 * @property string $mail_to_address_field_identifier
 * @property int|null $review_period
 * @param DateTimeInterface|null $review_deadline
 * @property-read Subsidy $subsidy
 * @property-read Collection<int, SubsidyStage> $subsidyStages
 * @property-read Collection<int, SubsidyStageTransitionMessage> $subsidyLetters
 * @property-read ?SubsidyStageTransitionMessage $publishedSubsidyLetter
 */

class SubsidyVersion extends Model
{
    use HasUuids;
    use HasFactory;
    use HasTimestamps;


    /**
     * @var string|null
     */
    protected $connection = Connection::APPLICATION;

    public const UPDATED_AT = null;

    protected $fillable = [
        'version',
        'status',
        'subsidy_page_url',
        'contact_mail_address',
        'mail_to_name_field_identifier',
        'mail_to_address_field_identifier',
    ];

    protected $casts = [
        'id' => 'string',
        'status' => VersionStatus::class,
        'review_deadline' => 'datetime'
    ];

    public function subsidy(): BelongsTo
    {
        return $this->belongsTo(Subsidy::class, 'subsidy_id', 'id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('created_at');
    }

    public function scopeOrderedByVersion(Builder $query): Builder
    {
        return $query->orderBy('version', 'desc');
    }

    public function subsidyStages(): HasMany
    {
        return $this->hasMany(SubsidyStage::class, 'subsidy_version_id', 'id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [VersionStatus::Published, VersionStatus::Archived]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', VersionStatus::Published);
    }

    public function scopeSubjectRole(Builder $query, SubjectRole $role): Builder
    {
        /** @phpstan-ignore-next-line */
        return $query->whereRelation('subsidyStages', fn (Builder $subQuery) => $subQuery->subjectRole($role));
    }

    protected static function newFactory(): SubsidyVersionFactory
    {
        return new SubsidyVersionFactory();
    }
}
