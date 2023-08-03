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
 * @property string $subsidy_stage_id
 * @property string $user_id
 * @property ApplicationStageVersionStatus $status
 * @property Application $application
 */
class ApplicationStage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $fillable = [
        'subsidy_stage_id',
        'user_id',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function applicationStageVersions(): HasMany
    {
        return $this->hasMany(ApplicationStageVersion::class, 'application_stage_id', 'id');
    }
}
