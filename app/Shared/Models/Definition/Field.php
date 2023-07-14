<?php

namespace App\Shared\Models\Definition;

use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Factories\FieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $code
 * @property-read string $title
 * @property-read ?string $description
 * @property-read FieldType $type
 * @property-read ?array $params
 * @property-read bool $is_required
 */
class Field extends Model
{
    use HasFactory;

    protected $connection = Connection::Form;

    protected $casts = [
        'type' => FieldType::class,
        'params' => 'json'
    ];

    protected $keyType = 'string';
    public $timestamps = false;

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    protected static function newFactory(): FieldFactory
    {
        return new FieldFactory();
    }
}
