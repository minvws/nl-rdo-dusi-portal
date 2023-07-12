<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Connection;
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

    public $timestamps = false;

    /**
     * @var string|null
     */
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
        return $this->hasMany(Form::class, 'subsidy_id', 'id');
    }

    public function publishedForm(): HasOne
    {
        return $this->hasOne(Form::class)->where('status', '=', 'published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('title');
    }
}
