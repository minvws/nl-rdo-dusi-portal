<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Connection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * User
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasUuids;
    use TwoFactorAuthenticatable;

    protected $connection = Connection::USER;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<array-key, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active_until' => 'timestamp',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'created_by');
    }
}
