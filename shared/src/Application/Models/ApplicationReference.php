<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ApplicationReference extends Model
{
    protected $primaryKey = 'reference';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $connection = Connection::APPLICATION;

    protected $fillable = [
        'reference',
        'used',
        'deleted',
    ];

    protected $casts = [
        'used' => 'boolean',
        'deleted' => 'boolean',
    ];

    public function scopeIsUsed(Builder $query, bool $used): Builder
    {
        return $query->where('used', '=', $used);
    }

    public function scopeIsDeleted(Builder $query, bool $deleted): Builder
    {
        return $query->where('used', '=', $deleted);
    }
}
