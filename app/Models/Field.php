<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasUuids;

    protected $connection = Connection::FORM;

    public const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'form_id',
        'title',
        'description',
        'type',
        'params',
        'is_required',
        'code',
        'source',
    ];

    protected $casts = [
        'params' => 'array'
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'field_id', 'id');
    }
}
