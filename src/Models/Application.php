<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTimeInterface;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationFactory;
use MinVWS\DUSi\Shared\Application\Shared\Models\Application\Identity;
use MinVWS\DUSi\Shared\Application\Shared\Models\Application\IdentityType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $subsidy_version_id
 * @property string $application_title
 * @property string $identity_type
 * @property string $identity_identifier
 * @property Identity $identity
 * @property DateTimeInterface $locked_from
 * @property DateTimeInterface $final_review_deadline
 */
class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'identity_type' => IdentityType::class,
        'locked_from' => 'datetime',
        'final_review_deadline' => 'date',
    ];

    protected $fillable = [
        'subsidy_version_id',
        'application_title',
        'final_review_deadline',
        'locked_from'
    ];

    public function applicationHashes(): HasMany
    {
        return $this->hasMany(ApplicationHash::class, 'application_id', 'id');
    }

    public function applicationStages(): HasMany
    {
        return $this->hasMany(ApplicationStage::class, 'application_id', 'id');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function identity(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => new Identity(
                IdentityType::from($attributes['identity_type']),
                $attributes['identity_identifier']
            ),
            set: fn (Identity $identity) => [
                'identity_type' => $identity->type,
                'identity_identifier' => $identity->identifier
            ]
        );
    }

    protected static function newFactory(): ApplicationFactory
    {
        return new ApplicationFactory();
    }
}
