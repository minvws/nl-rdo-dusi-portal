<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Connection;
use App\Models\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $form_id
 * @property int $version
 * @property VersionStatus $status
 * @property array $ui
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
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }
}
