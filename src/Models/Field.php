<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\FieldFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;

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

    public function fieldGroups(): BelongsTo
    {
        return $this->belongsTo(FieldGroup::class, 'field_group_id', 'id');
    }

    public function subsidyStageHashFields(): HasMany
    {
        return $this->HasMany(SubsidyStageHashField::class, 'field_id', 'id');
    }

    protected static function newFactory(): FieldFactory
    {
        return new FieldFactory();
    }
}
