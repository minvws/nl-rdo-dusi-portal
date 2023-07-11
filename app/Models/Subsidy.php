<?php

namespace App\Models;

use App\Models\Connection;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read string $id
 * @property-read string $title
 * @property-read string $description
 * @property-read DateTimeInterface $valid_from
 * @property-read ?DateTimeInterface $valid_to
 */
class Subsidy extends Model
{
    use HasFactory;

    protected $connection = Connection::FORM;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'valid_from',
        'valid_to',
    ];
    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date'
    ];

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
    }

    public function publishedForm(): HasOne
    {
        return $this->hasOne(Form::class)->where('status', '=', 'published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('title');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereRelation('forms', fn (Builder $subQuery) => $subQuery->open());
    }
}
