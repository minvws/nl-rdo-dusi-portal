<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read string $id
 * @property-read VersionStatus $status
 * @property-read int $version
 * @property-read ?FormUI $publishedUI
 */
class Form extends Model
{
    use HasFactory;

    protected $connection = Connection::Form;

    protected $casts = [
        'status' => VersionStatus::class
    ];

    protected $keyType = 'string';

    public function subsidy(): BelongsTo
    {
        return $this->belongsTo(Subsidy::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function uis(): HasMany
    {
        return $this->hasMany(FormUI::class);
    }

    public function publishedUI(): HasOne
    {
        return $this->hasOne(FormUI::class)->where('status', '=', 'published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('version', 'desc');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [VersionStatus::Published, VersionStatus::Archived]);
    }
}
