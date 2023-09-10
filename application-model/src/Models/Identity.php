<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use MinVWS\DUSi\Shared\Application\Database\Factories\IdentityFactory;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

/**
 * @property string $id
 * @property IdentityType $type
 * @property string $encrypted_identifier
 * @property string $hashed_identifier
 * @property-read Collection<Application> $applications
 * @property-read Collection<ApplicationMessage> $applicationMessages
 */
class Identity extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::APPLICATION;

    protected $casts = [
        'identity_type' => IdentityType::class,
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'identity_id', 'id');
    }

    public function applicationMessages(): HasManyThrough
    {
        return $this->hasManyThrough(
            ApplicationMessage::class,
            Application::class,
            'identity_id',
            'application_id',
            'id',
            'id'
        );
    }

    protected static function newFactory(): IdentityFactory
    {
        return new IdentityFactory();
    }
}
