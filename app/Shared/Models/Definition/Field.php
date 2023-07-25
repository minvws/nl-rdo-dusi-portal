<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition;

use App\Shared\Models\Definition\Enums\FieldType;
use App\Shared\Models\Definition\Enums\FieldSource;
use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Factories\FieldFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    public $timestamps = false;

    protected $casts = [
        'type' => FieldType::class,
        'source' => FieldSource::class,
        'params' => 'array',
        'is_required' => 'boolean',
    ];

    protected $fillable = [
        'title',
        'description',
        'type',
        'params',
        'is_required',
        'code',
        'source',
    ];

    public function subsidyStages(): BelongsToMany
    {
        return $this->belongsToMany(SubsidyStage::class);
    }

    protected static function newFactory(): FieldFactory
    {
        return new FieldFactory();
    }
}
