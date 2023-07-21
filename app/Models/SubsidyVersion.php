<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Connection;
use App\Models\Enums\VersionStatus;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $title
 * @property string $description
 * @property DateTimeInterface $valid_from
 * @property DateTimeInterface $valid_to
 * @property SubsidyStage[] $forms
 */

class SubsidyVersion extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    public const UPDATED_AT = null;

    protected $fillable = [
        'version',
        'status',
    ];

    protected $casts = [
        'status' => VersionStatus::class
    ];

    public function subsidy(): BelongsTo
    {
        return $this->belongsTo(Subsidy::class, 'subsidy_id', 'id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('created_at');
    }

    public function subsidyStages(): HasMany
    {
        return $this->hasMany(SubsidyStage::class, 'subsidy_version_id', 'id');
    }
}
