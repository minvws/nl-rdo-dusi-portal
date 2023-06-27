<?php

namespace App\Models\Definition;

use App\Models\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read FieldType $type
 * @property-read boolean $is_required
 */
class Field extends Model
{
    use HasFactory;

    protected $connection = Connection::Form;

    protected $casts = [
        'type' => FieldType::class
    ];

    protected $keyType = 'string';
    public $timestamps = false;

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort');
    }
}
