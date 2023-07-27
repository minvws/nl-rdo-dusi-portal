<?php

declare(strict_types=1);

namespace MinVWS\SubsidyModel\Models;

use MinVWS\SubsidyModel\Models\Enums\FieldSource;
use MinVWS\SubsidyModel\Models\Enums\FieldType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
