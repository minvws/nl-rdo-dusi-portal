<?php

namespace App\Models;

use App\Models\Connection;
use App\Models\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read VersionStatus $status
 * @property-read int $version
 * @property-read string $ui
 */
class FormUI extends Model
{
    use HasFactory;

    protected $table = 'form_uis';
    protected $connection = Connection::FORM;

    protected $fillable = [
        'form_id',
        'version',
        'status',
        'ui',
    ];

    protected $casts = [
        'status' => VersionStatus::class,
        'ui' => 'json'
    ];

    protected $keyType = 'string';

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
