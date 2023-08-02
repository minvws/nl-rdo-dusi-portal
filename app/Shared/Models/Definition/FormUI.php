<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition;

use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Factories\FormUIFactory;
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

    protected $casts = [
        'status' => VersionStatus::class,
        'ui' => 'json'
    ];

    protected $keyType = 'string';

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    protected static function newFactory(): FormUIFactory
    {
        return new FormUIFactory();
    }
}
