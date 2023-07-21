<?php

namespace App\Models;

use App\Models\Enums\VersionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldGroupUI extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    protected $casts = [
        'default_input_ui' => 'json',
        'default_review_ui' => 'json',
        'status' => VersionStatus::class
    ];

    protected $fillable = [
        'field_group_id',
        'version',
        'status',
        'default_input_ui',
        'default_review_ui',
    ];

    public function fieldGroup(): BelongsTo
    {
        return $this->belongsTo(FieldGroup::class, 'field_group_id', 'id');
    }
}
