<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Connection;
use App\Models\Enums\FieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $form_id
 * @property string $code
 * @property string $title
 * @property string $description
 * @property FieldType $type
 * @property array $params
 * @property bool $is_required
 * @property string $source
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
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }
}
