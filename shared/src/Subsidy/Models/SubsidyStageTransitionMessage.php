<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageTransitionMessageFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

/**
 * @property string $id
 * @property string $subsidy_transition_stage_id
 * @property int $version
 * @property string $status
 * @property string $subject
 * @property string $content_html
 * @property string $content_pdf
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 * @property-read SubsidyStageTransition $subsidyStageTransition
 */
class SubsidyStageTransitionMessage extends Model
{
    use HasUuids;
    use HasFactory;
    use HasTimestamps;

    /**
     * @var string|null
     */
    protected $connection = Connection::APPLICATION;


    protected $fillable = [
        'version',
        'status',
        'subject',
        'content_pdf',
        'content_html',
    ];

    protected $casts = [
        'id' => 'string',
        'status' => VersionStatus::class,
    ];

    public function subsidyStageTransition(): BelongsTo
    {
        return $this->belongsTo(SubsidyStageTransition::class);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->max('version');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    protected static function newFactory(): SubsidyStageTransitionMessageFactory
    {
        return new SubsidyStageTransitionMessageFactory();
    }
}
