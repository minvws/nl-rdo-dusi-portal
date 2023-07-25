<?php

declare(strict_types=1);

namespace App\Shared\Models\Definition;

use App\Shared\Models\Definition\SubsidyStage;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubsidyStageUi extends Model
{
    use HasFactory;
    use HasUuids;

    protected $casts = [
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'ui' => 'array'
    ];
    protected $fillable = [
        'id',
        'subsidy_stage_id',
        'version',
        'status',
        'ui',
    ];

    public function subsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'subsidy_stage_id', 'id');
    }

    protected static function newFactory(): SubsidyStageUiFactory
    {
        return new FieldFactory();
    }
}
