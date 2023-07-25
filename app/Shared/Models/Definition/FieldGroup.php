<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition;

use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldGroup extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    protected $casts = [
        'status' => VersionStatus::class,
    ];

    protected $fillable = [
        'version',
        'status',
        'title',
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
