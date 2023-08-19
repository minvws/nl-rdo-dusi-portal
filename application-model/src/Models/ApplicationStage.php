<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationStageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;

/**
 * @property string $id
 * @property string $subsidy_stage_id
 * @property string $user_id
 * @property int $stage
 * @property Application $application
 * @property SubsidyStage $subsidyStage
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
        return $this->hasMany(ApplicationStageVersion::class, 'application_stage_id', 'id')
            ->orderBy('version', 'asc');
    }

    public function latestApplicationStageVersion(): HasOne
    {
        return $this->hasOne(ApplicationStageVersion::class)
            ->orderBy('version', 'desc')->limit(1);
    }

    public function subsidyStage(): BelongsTo
    {
        return $this->belongsTo(SubsidyStage::class, 'subsidy_stage_id', 'id');
    }

    protected static function newFactory(): ApplicationStageFactory
    {
        return ApplicationStageFactory::new();
    }
}
