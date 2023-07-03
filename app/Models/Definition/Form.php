<?php

namespace App\Models\Definition;

use App\Models\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read FormStatus $status
 * @property-read Collection<Field> $fields
 */
class Form extends Model
{
    use HasFactory;

    protected $connection = Connection::Form;

    protected $casts = [
        'status' => FormStatus::class
    ];

    protected $keyType = 'string';

    public function subsidy(): BelongsTo
    {
        return $this->belongsTo(Subsidy::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class)->ordered();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('version', 'desc');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [FormStatus::Published, FormStatus::Archived]);
    }
}
