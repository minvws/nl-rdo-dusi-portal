<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Application\Database\Factories\AnswerFactory;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

/**
 * @property string $id
 * @property string $application_stage_id
 * @property string $field_id
 * @property string $encrypted_answer
 * @property-read ApplicationStage $applicationStage
 * @property-read Field $field
 */
class Answer extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    public const UPDATED_AT = null;

    public function applicationStage(): BelongsTo
    {
        return $this->belongsTo(ApplicationStage::class, 'application_stage_id', 'id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id', 'id');
    }

    protected static function newFactory(): AnswerFactory
    {
        return AnswerFactory::new();
    }
}
