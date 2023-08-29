<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasUuids;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active_until',
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
        'active_until' => 'datetime',
    ];

    /**
     * User that created this user.
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'created_by');
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, 'organisation_role')
            ->using(OrganisationRole::class)
            ->withPivot('role_name');
    }

    public function isAdministrator(): bool
    {
        return $this->organisations()
            ->wherePivot('role_name', 'admin')
            ->exists();
    }

    public function getActiveAttribute(): bool
    {
        if ($this->active_until === null) {
            return true;
        }

        if (!($this->active_until instanceof Carbon)) {
            return false;
        }

        return $this->active_until->isFuture();
    }

    public function twoFactorQrCodeSvgWithAria(): string
    {
        $svgTag = $this->twoFactorQrCodeSvg();
        return str_replace('<svg ', '<svg role="img"focusable="false" aria-label="QR-code" ', $svgTag);
    }
}
