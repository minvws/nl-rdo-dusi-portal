<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Database\Factories\UserFactory;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $organisation_id
 * @property CarbonImmutable $active_until
 * @property bool $active
 * @property ?CarbonImmutable $password_updated_at
 * @property ?string $password_reset_token
 * @property ?CarbonImmutable $password_reset_token_valid_until
 * @property Collection<int, Role> $roles
 * @property Organisation $organisation
 * @method bool can($abilities, $arguments = [])
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
        'active_until' => 'immutable_datetime',
        'password_updated_at' => 'immutable_datetime',
        'password_reset_token_valid_until' => 'immutable_datetime',
    ];

    protected $with = [
        'roles',
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
            ->using(RoleUser::class)
            ->withPivot('subsidy_id');
    }

    public function attachRole(RoleEnum $role, string $subsidyId = null): void
    {
        if (
            $this->roles()
                ->where('name', $role->value)
                ->wherePivot('subsidy_id', $subsidyId)
                ->exists()
        ) {
            return;
        }

        $this->roles()->attach($role->value, ['subsidy_id' => $subsidyId]);
    }

    public function detachRole(RoleEnum $role, string $subsidyId = null): void
    {
        $this->roles()
            ->wherePivot('subsidy_id', $subsidyId)
            ->detach($role->value);
    }

    public function isUserAdministrator(): bool
    {
        return $this->hasRole(RoleEnum::UserAdmin);
    }

    /**
     * @return Collection<int, Role>
     */
    private function getRolesForSubsidy(Subsidy|string $subsidyId): Collection
    {
        $subsidyId = $subsidyId instanceof Subsidy ? $subsidyId->id : $subsidyId;

        return $this->roles
            ->filter(fn (Role $userRole) =>
                $userRole->pivot->subsidy_id === $subsidyId || $userRole->pivot->subsidy_id === null);
    }

    public function hasRoleToViewAllStagesForSubsidy(Subsidy|string $subsidyId): bool
    {
        return
            $this->getRolesForSubsidy($subsidyId)
                ->filter(fn (Role $userRole) => $userRole->view_all_stages)
                ->isNotEmpty();
    }

    public function hasRoleForSubsidy(RoleEnum|array $role, Subsidy|string $subsidyId): bool
    {
        $roles = is_array($role) ? $role : [$role];

        return
            $this->getRolesForSubsidy($subsidyId)
                ->filter(fn (Role $userRole) => in_array($userRole->name, $roles, true))
                ->isNotEmpty();
    }

    public function hasRole(RoleEnum|array $role): bool
    {
        $roles = is_array($role) ? $role : [$role];

        return
            $this->roles
                ->filter(fn (Role $userRole) => in_array($userRole->name, $roles, true))
                ->isNotEmpty();
    }

    public function getActiveAttribute(): bool
    {
        if ($this->active_until === null) {
            return true;
        }

        if (!($this->active_until instanceof CarbonImmutable)) {
            return false;
        }

        return $this->active_until->isFuture();
    }

    public function passwordExpired(int $withinDays = 0): bool
    {
        if ($this->password_updated_at === null) {
            return true;
        }

        if (!($this->password_updated_at instanceof CarbonImmutable)) {
            return true;
        }

        // Check if password expires within x days of 180 days
        return $this->password_updated_at
            ->addDays(180)
            ->subDays($withinDays)
            ->isPast();
    }

    public function twoFactorQrCodeSvgWithAria(): string
    {
        $svgTag = $this->twoFactorQrCodeSvg();
        return str_replace('<svg ', '<svg role="img" focusable="false" aria-label="QR-code" ', $svgTag);
    }

    public function scopeFilterByNameOrEmail(Builder $query, ?string $filter = null): void
    {
        $query->when(
            $filter,
            fn () => $query->where(function (Builder $query) use ($filter) {
                $query
                    ->where('name', 'like', "%$filter%")
                    ->orWhere('email', 'like', "%$filter%");
            })
        );
    }

    protected static function newFactory(): UserFactory
    {
        return new UserFactory();
    }
}
