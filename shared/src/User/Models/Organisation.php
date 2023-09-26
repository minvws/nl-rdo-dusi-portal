<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MinVWS\DUSi\Shared\User\Database\Factories\OrganisationFactory;

/**
 * @property string $name
 * @property Collection<User> $users
 */
class Organisation extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::USER;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected static function newFactory(): OrganisationFactory
    {
        return new OrganisationFactory();
    }
}
