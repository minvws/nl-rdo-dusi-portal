<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Application\Database\Factories\AnswerFactory;

/**
 * @property string $id
 * @property string $encrypted_answer
 * @property string $field_id
 */
class Answer extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'field_id',
        'encrypted_answer',
    ];

    public function applicationStageVersion(): BelongsTo
    {
        return $this->belongsTo(ApplicationStageVersion::class, 'application_stage_version_id', 'id');
    }

    protected static function newFactory(): AnswerFactory
    {
        return AnswerFactory::new();
    }
}
