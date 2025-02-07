<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MinVWS\DUSi\Shared\User\Database\Factories\RoleFactory;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;

/**
 * @property RoleEnum $name
 * @property bool $view_all_stages
 * @property-read RoleUser $pivot
 */
class Role extends Model
{
    use HasFactory;

    protected $connection = Connection::USER;
    protected $primaryKey = 'name';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'view_all_stages',
    ];

    protected $casts = [
        'name' => RoleEnum::class
    ];

    protected static function newFactory(): RoleFactory
    {
        return new RoleFactory();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->using(RoleUser::class)
            ->withPivot('subsidy_id');
    }
}
