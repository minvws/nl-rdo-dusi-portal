<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageUIFactory;

/**
 * @property array $input_ui
 * @property array $view_ui
 */
class SubsidyStageUI extends Model
{
    use HasUuids;
    use HasFactory;

    protected $connection = Connection::APPLICATION;

    protected $table = 'subsidy_stage_uis';

    protected $casts = [
        'id' => 'string',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'input_ui' => 'array',
        'view_ui' => 'array',
    ];
    protected $fillable = [
        'id',
        'subsidy_stage_id',
        'version',
        'status',
        'input_ui',
        'view_ui',
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
