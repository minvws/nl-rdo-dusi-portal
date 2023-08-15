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
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

/**
 * @property string $id
 * @property string $subsidy_version_id
 * @property int $version
 * @property string $status
 * @property string $content_pdf
 * @property string $content_view
 * @property string $created_at
 * @property-read SubsidyVersion $subsidyVersion
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
        'content_pdf',
        'content_view',
    ];

    protected $casts = [
        'id' => 'string',
        'status' => VersionStatus::class
    ];

    public function subsidyVersion(): BelongsTo
    {
        return $this->belongsTo(SubsidyVersion::class);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->max('version');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    protected static function newFactory(): SubsidyLetterFactory
    {
        return new SubsidyLetterFactory();
    }
}
