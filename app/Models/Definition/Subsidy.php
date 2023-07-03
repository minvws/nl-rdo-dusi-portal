<?php

namespace App\Models\Definition;

use App\Models\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subsidy extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $connection = Connection::Form;
    protected $keyType = 'string';

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
