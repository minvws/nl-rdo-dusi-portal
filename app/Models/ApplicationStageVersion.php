<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\ApplicationStageVersionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property integer $version
 */
class ApplicationStageVersion extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'status' => ApplicationStageVersionStatus::class,
    ];

    protected $fillable = [
        'status',
        'version'
    ];

    public const UPDATED_AT = null;

    public function applicationStage(): BelongsTo
    {
        return $this->belongsTo(ApplicationStage::class, 'application_stage_id', 'id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'application_stage_version_id', 'id');
    }
}
