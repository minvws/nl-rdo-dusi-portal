<?php

namespace App\Models;

use App\Models\Connection;
use App\Models\Enums\FieldType;
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
 */
class Field extends Model
{
    use HasFactory;

    protected $connection = Connection::FORM;

    protected $fillable = [
        'form_id',
        'code',
        'title',
        'description',
        'type',
        'params',
        'is_required',
        'source',
    ];

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
}
