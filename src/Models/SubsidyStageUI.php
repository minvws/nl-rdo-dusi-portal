<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageUIFactory;

class SubsidyStageUI extends Model
{
    use HasUuids;
    use HasFactory;

    protected $table = 'subsidy_stage_uis';

    protected $casts = [
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'ui' => 'json'
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

    protected static function newFactory(): SubsidyStageUIFactory
    {
        return new SubsidyStageUIFactory();
    }
}
