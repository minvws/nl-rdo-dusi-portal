<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationMessageFactory;

/**
 * @property string $id
 * @property string $application_id
 * @property string $application_stage_transition_id
 * @property string $subject
 * @property boolean $is_new
 * @property string $html_path
 * @property string $pdf_path
 * @property DateTime $seen_at
 * @property DateTime $sent_at
 * @property-read Application $application
 * @property-read ApplicationStageTransition $applicationStageTransition
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationMessage extends Model
{
    use HasFactory;
    use HasUuids;

    public $timestamps = false;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'is_new' => 'boolean',
        'seen_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected $fillable = [
        'html_path',
        'is_new',
        'pdf_path',
        'subject',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function applicationStageTransition(): BelongsTo
    {
        return $this->belongsTo(
            ApplicationStageTransition::class,
            'application_stage_transition_id',
            'id'
        );
    }

    protected function scopeForIdentity(Builder $builder, Identity $identity): void
    {
        $builder->whereHas('application', function (Builder $subBuilder) use ($identity) {
            $subBuilder->scopes(['forIdentity' => $identity]);
        });
    }

    protected static function booting(): void
    {
        self::creating(static function (self $self) {
            $self->setAttribute('sent_at', $self->freshTimestamp());
        });

        self::updating(static function (self $self) {
            if ($self->isDirty('is_new')) {
                $self->setAttribute('seen_at', $self->freshTimestamp());
            }
        });
    }

    protected static function newFactory(): ApplicationMessageFactory
    {
        return ApplicationMessageFactory::new();
    }
}
