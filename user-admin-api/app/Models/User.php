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
        'organisation_id',
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
        'password_updated_at' => 'timestamp',
    ];

    /**
     * User that created this user.
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'created_by');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot('subsidy_id');
    }

    public function attachRole(string $role, string $subsidyId = null): void
    {
        if (
            $this->roles()
            ->where('name', $role)
            ->wherePivot('subsidy_id', $subsidyId)
            ->exists()
        ) {
            return;
        }

        $this->roles()->attach($role, ['subsidy_id' => $subsidyId]);
    }

    public function detachRole(string $role, string $subsidyId = null): void
    {
        $this->roles()
            ->where('name', $role)
            ->wherePivot('subsidy_id', $subsidyId)
            ->detach();
    }

    public function isAdministrator(): bool
    {
        return $this->hasRole('admin');
    }

    public function hasRole(string $roleName, ?string $subsidyId = null): bool
    {
        return $this->roles()
            ->where('role_name', $roleName)
            ->wherePivot('subsidy_id', $subsidyId)
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
