<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Connection;
use App\Models\Enums\VersionStatus;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $title
 * @property string $description
 * @property DateTimeInterface $valid_from
 * @property DateTimeInterface $valid_to
 * @property Form[] $forms
 */

class Subsidy extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    protected $fillable = [
        'title',
        'description',
        'valid_from',
        'valid_to',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => VersionStatus::class,
        'valid_from' => 'timestamp',
        'valid_to' => 'timestamp',
    ];

    public function subsidyVersions(): HasMany
    {
        return $this->hasMany(SubsidyVersion::class, 'subsidy_id', 'id');
    }

    public function publishedVersion(): HasOne
    {
        return $this->hasOne(SubsidyVersion::class)->where('status', '=', 'published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('title');
    }
}
