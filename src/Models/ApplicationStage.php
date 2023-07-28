<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationStageFactory;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $subsidy_stage_id
 * @property string $user_id
 * @property ApplicationStageStatus $status
 * @property Application $application
 */
class ApplicationStage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'status' => ApplicationStageStatus::class,
    ];
    protected $fillable = [
        'subsidy_stage_id',
        'user_id',
        'status',
    ];


    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function applicationStageVersions(): HasMany
    {
        return $this->hasMany(ApplicationStageVersion::class, 'application_stages_id', 'id');
    }

    protected static function newFactory(): ApplicationStageFactory
    {
        return ApplicationStageFactory::new();
    }
}
