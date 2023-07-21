<?php

namespace App\Models;

use App\Models\Enums\FieldSource;
use App\Models\Enums\FieldType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use HasFactory;
    use HasUuids;

    public $timestamps = false;

    protected $casts = [
        'type' => FieldType::class,
        'source' => FieldSource::class,
        'params' => 'json',
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

    public function subsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'subsidy_stage_id', 'id');
    }

    public function fieldGroups()
    {
        return $this->HasMany(FieldGroup::class, 'field_id', 'id');
    }

    public function subsidyStageHashFields()
    {
        return $this->HasMany(SubsidyStageHashField::class, 'field_id', 'id');
    }
}
