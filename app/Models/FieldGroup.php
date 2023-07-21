<?php

namespace App\Models;

use App\Models\Enums\FieldSource;
use App\Models\Enums\FieldStatus;
use App\Models\Enums\FieldType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldGroup extends Model
{
    use HasFactory;
    use HasUuids;

    protected $casts = [
        'type' => FieldType::class,
        'source' => FieldSource::class,
        'status' => FieldStatus::class,
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $fillable = [
        'version',
        'status',
        'title',
        'created_at',
        'updated_at'
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'field_group_id', 'id');
    }

    public function fieldGroupPurpose(): BelongsTo
    {
        return $this->belongsTo(FieldGroupPurpose::class, 'purpose', 'id');
    }
}
