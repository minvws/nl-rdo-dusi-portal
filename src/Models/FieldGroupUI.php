<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class FieldGroupUI extends Model
{
    use HasUuids;
    use HasFactory;


    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    protected $casts = [
        'default_input_ui' => 'array',
        'default_review_ui' => 'array',
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
