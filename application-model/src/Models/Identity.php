<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\Application\Database\Factories\IdentityFactory;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

/**
 * @property string $id
 * @property IdentityType $type
 * @property string $encrypted_identifier
 * @property string $hashed_identifier
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
        return $this->hasMany(Application::class, 'application_id', 'id');
    }

    protected static function newFactory(): IdentityFactory
    {
        return new IdentityFactory();
    }
}
