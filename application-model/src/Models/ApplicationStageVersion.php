<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationStageVersionFactory;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionDecision;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;

/**
 * @property string $id
 * @property integer $version
 * @property ApplicationStageVersionStatus $status
 * @property-read ApplicationStage $applicationStage
 * @property-read Collection<Answer> $answers
 * @property ?string $pdf_letter_path
 * @property ?string $view_letter_path
 * @property ApplicationStageVersionDecision $decision
 * @property ?string $assessor_user_id
 * @property DateTime $decision_updated_at
 */
class ApplicationStageVersion extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'status' => ApplicationStageVersionStatus::class,
        'decision' => ApplicationStageVersionDecision::class,
        'decision_updated_at' => 'datetime'
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

    protected static function newFactory(): ApplicationStageVersionFactory
    {
        return new ApplicationStageVersionFactory();
    }
}
