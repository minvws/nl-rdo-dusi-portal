<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyLetterFactory;

/**
 * @property string $id
 * @property string $subsidy_version_id
 * @property int $version
 * @property string $status
 * @property string $content
 * @property string $created_at
 */

class SubsidyLetter extends Model
{
    use HasUuids;
    use HasFactory;
    use HasTimestamps;


    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;


    protected $fillable = [
        'version',
        'status',
        'content',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->max('version');
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRequestedForChanges(Builder $query): Builder
    {
        return $query->where('status', 'request_for_changes');
    }

    protected static function newFactory(): SubsidyLetterFactory
    {
        return new SubsidyLetterFactory();
    }
}
