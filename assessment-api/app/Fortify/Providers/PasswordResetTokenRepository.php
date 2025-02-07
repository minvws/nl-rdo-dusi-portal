<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Str;
use MinVWS\DUSi\Shared\User\Models\User;
use RuntimeException;

class PasswordResetTokenRepository implements TokenRepositoryInterface
{
    public function __construct(
        protected HasherContract $hasher,
        protected string $hashKey,
        protected int $expires = 60,
        protected int $throttle = 60
    ) {
        $this->expires = $expires * 60;
    }

    public function create(CanResetPasswordContract $user): string
    {
        if (!($user instanceof User)) {
            throw new RuntimeException('User variable is not of type User');
        }

        $token = $this->createNewToken();

        $user->password_reset_token = $this->hasher->make($token);
        $user->password_reset_token_valid_until = CarbonImmutable::now()->addSeconds($this->expires);
        $user->update();

        return $token;
    }

    public function exists(CanResetPasswordContract $user, $token): bool
    {
        if (!($user instanceof User)) {
            throw new RuntimeException('User variable is not of type User');
        }

        return $user->password_reset_token &&
            $user->password_reset_token_valid_until &&
            ! $this->tokenExpired($user->password_reset_token_valid_until) &&
            $this->hasher->check($token, $user->password_reset_token);
    }

    public function recentlyCreatedToken(CanResetPasswordContract $user): bool
    {
        if (!($user instanceof User)) {
            throw new RuntimeException('User variable is not of type User');
        }

        if (!$user->password_reset_token_valid_until) {
            return false;
        }

        return $user->password_reset_token_valid_until
            ->subSeconds($this->expires)
            ->addSeconds($this->throttle)
            ->isFuture();
    }

    public function delete(CanResetPasswordContract $user): void
    {
        $this->deleteExisting($user);
    }

    public function deleteExpired(): void
    {
        $expiredAt = CarbonImmutable::now();

        User::query()->where('password_reset_token_valid_until', '<', $expiredAt)->update([
            'password_reset_token' => null,
            'password_reset_token_valid_until' => null,
        ]);
    }

    protected function deleteExisting(CanResetPasswordContract $user): int
    {
        if (!($user instanceof User)) {
            throw new RuntimeException('User variable is not of type User');
        }

        $user->password_reset_token = null;
        $user->password_reset_token_valid_until = null;
        $user->update();

        return 0;
    }

    /**
     * Create a new token for the user.
     * Same as DatabaseTokenRepository
     *
     * @return string
     */
    protected function createNewToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * Check if token is expired.
     *
     * @param CarbonImmutable $validUntil
     * @return bool
     */
    protected function tokenExpired(CarbonImmutable $validUntil): bool
    {
        return $validUntil->isPast();
    }
}
