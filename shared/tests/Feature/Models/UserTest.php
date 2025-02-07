<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Models;

use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Models\User;

class UserTest extends TestCase
{
    public function testPasswordNotExpired(): void
    {
        $user = User::factory()->create([
            'password_updated_at' => now(),
        ]);

        $this->assertFalse($user->passwordExpired());
    }

    public function testPasswordExpired(): void
    {
        $user = User::factory()->create([
            'password_updated_at' => now()->subDays(180),
        ]);

        $this->assertTrue($user->passwordExpired());
    }
}
