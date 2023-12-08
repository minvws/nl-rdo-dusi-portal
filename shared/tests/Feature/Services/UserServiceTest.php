<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Services;

use Illuminate\Support\Facades\Hash;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\User\Services\UserService;

class UserServiceTest extends TestCase
{
    public function testGetSubsidiesForUser(): void
    {
        Role::factory()->withRoleFromEnum(RoleEnum::UserAdmin);
        Role::factory()->withRoleFromEnum(RoleEnum::Assessor);

        $subsidyA = Subsidy::factory()->create();
        $subsidyB = Subsidy::factory()->create();

        $user = User::factory()->create([
            'name' => 'New User',
            'password' => Hash::make('password'),
        ]);
        $user->attachRole(RoleEnum::Assessor, $subsidyA->id);
        $user->attachRole(RoleEnum::UserAdmin, $subsidyB->id);
        $user->attachRole(RoleEnum::Assessor, $subsidyB->id);

        $userService = app(UserService::class);
        $subsidiesForUser = $userService->getSubsidiesForUser($user);
        $this->assertCount(2, $subsidiesForUser);
        $this->assertContains($subsidyA->id, $subsidiesForUser->pluck('id'));
        $this->assertContains($subsidyB->id, $subsidiesForUser->pluck('id'));
    }
}
