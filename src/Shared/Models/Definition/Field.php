<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Shared\Models\Definition;

use MinVWS\DUSi\Shared\Application\Shared\Models\Definition\Enums\FieldType;
use MinVWS\DUSi\Shared\Application\Shared\Models\Definition\Enums\FieldSource;
use MinVWS\DUSi\Shared\Application\Shared\Models\Connection;
use MinVWS\DUSi\Shared\Application\Shared\Models\Definition\Factories\FieldFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $title
 * @property string $description
 * @property FieldType $type
 * @property boolean $is_required
 * @property string $code
 * @property FieldSource $source
 * @property array $params
 * @property string $id
 */
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
