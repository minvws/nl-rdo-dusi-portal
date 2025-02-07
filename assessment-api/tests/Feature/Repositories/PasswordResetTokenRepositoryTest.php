<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Str;
use MinVWS\DUSi\Assessment\API\Fortify\Providers\PasswordResetTokenRepository;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Models\User;

class PasswordResetTokenRepositoryTest extends TestCase
{
    protected PasswordResetTokenRepository $passwordResetTokenRepository;
    protected Hasher $hasher;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher = new BcryptHasher();
        $this->passwordResetTokenRepository = new PasswordResetTokenRepository(
            hasher: $this->hasher,
            hashKey: Str::random(16),
            expires: 60,
            throttle: 60,
        );

        $this->user = User::factory([
            'password_reset_token' => null,
            'password_reset_token_valid_until' => null,
        ])->create();
    }

    public function testTokenIsCreated(): void
    {
        $token = $this->passwordResetTokenRepository->create($this->user);

        $this->assertNotEmpty($this->user->password_reset_token);
        $this->assertNotEmpty($this->user->password_reset_token_valid_until);

        $this->assertNotEmpty($token);
        $this->assertTrue($this->hasher->check($token, $this->user->password_reset_token));
    }

    public function testTokenExists(): void
    {
        $token = $this->passwordResetTokenRepository->create($this->user);

        $this->assertTrue($this->passwordResetTokenRepository->exists($this->user, $token));
    }

    public function testTokenExpired(): void
    {
        CarbonImmutable::setTestNowAndTimezone('2023-10-01 13:37:00');

        $token = $this->passwordResetTokenRepository->create($this->user);

        // 1 hour later the token should not be valid anymore
        CarbonImmutable::setTestNowAndTimezone('2023-10-01 14:37:01');

        $this->assertFalse($this->passwordResetTokenRepository->exists($this->user, $token));
    }

    public function testTokenCanBeDeleted(): void
    {
        $this->passwordResetTokenRepository->create($this->user);

        $this->assertNotEmpty($this->user->password_reset_token);
        $this->assertNotEmpty($this->user->password_reset_token_valid_until);

        $this->passwordResetTokenRepository->delete($this->user);

        $this->assertEmpty($this->user->password_reset_token);
        $this->assertEmpty($this->user->password_reset_token_valid_until);
    }

    public function testDeleteExpiredTokens(): void
    {
        CarbonImmutable::setTestNowAndTimezone('2023-10-01 13:37:00');

        $this->passwordResetTokenRepository->create($this->user);

        // 1 hour later the token should not be valid anymore
        CarbonImmutable::setTestNowAndTimezone('2023-10-01 14:45:01');

        // Delete every old password token
        $this->passwordResetTokenRepository->deleteExpired();

        // Reload user from db and check if fields are empty
        $this->user->refresh();

        $this->assertEmpty($this->user->password_reset_token);
        $this->assertEmpty($this->user->password_reset_token_valid_until);
    }
}
