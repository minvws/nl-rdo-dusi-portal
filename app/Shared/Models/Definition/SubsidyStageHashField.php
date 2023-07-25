<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubsidyStageHashField extends Model
{
    use HasCompositePrimaryKey;

    public $timestamps = false;

    protected $primaryKey = ['subsidy_stage_hash_id', 'field_id'];

    public function subsidyStageHash(): BelongsTo
    {
        return $this->belongsTo(SubsidyStageHash::class, 'subsidy_stage_hash_id', 'id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id', 'id');
    }
}
