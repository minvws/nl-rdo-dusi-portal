<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use MinVWS\DUSi\Assessment\API\Fortify\Providers\PasswordResetTokenRepository;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Models\User;

class PasswordResetTest extends TestCase
{
    protected PasswordResetTokenRepository $resetTokenRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake email
        Notification::fake();

        // Setup roles
        Role::factory()->withRoleFromEnum(RoleEnum::UserAdmin);
        Role::factory()->withRoleFromEnum(RoleEnum::Assessor);

        $passwordBroker = app('auth.password')->broker('users');
        $this->resetTokenRepository = $passwordBroker->getRepository();
    }

    public function testUserNotFound(): void
    {
        $this
            ->json('POST', route('password.email'), [
                'email' => 'user@example.com',
            ])
            ->assertStatus(200)
            ->assertJson($this->passwordResetRequestResponse());
    }

    /**
     * @dataProvider notAllowedRolesProvider
     * @return void
     */
    public function testUserNotFoundWhenRoleNotMatch(RoleEnum $role)
    {
        $user = User::factory()->create([
            'name' => 'New User',
            'password' => Hash::make('password'),
        ]);

        $user->attachRole($role);

        $this
            ->json('POST', route('password.email'), [
                'email' => $user->email,
            ])
            ->assertStatus(200)
            ->assertJson($this->passwordResetRequestResponse());

        Notification::assertNothingSent();
    }

    public function testUserReceivesPasswordResetEmail()
    {
        // Create assessor user
        $user = User::factory()->create([
            'name' => 'New User',
            'password' => Hash::make('password'),
        ]);
        $user->attachRole(RoleEnum::Assessor);

        // Password reset request
        $this
            ->json('POST', route('password.email'), [
                'email' => $user->email,
            ])
            ->assertOk()
            ->assertJson($this->passwordResetRequestResponse());


        // Assert that a notification was sent...
        Notification::assertSentTo(
            [$user],
            ResetPassword::class
        );
        Notification::assertCount(1);
    }

    public function testUserCanResetPasswordWithToken()
    {
        // Create assessor user
        $user = User::factory()->create([
            'name' => 'New User',
            'password' => Hash::make('password'),
        ]);
        $user->attachRole(RoleEnum::Assessor);

        // Create token
        $token = $this->resetTokenRepository->create($user);

        // Password reset request
        $this
            ->json('POST', route('password.update'), [
                'email' => $user->email,
                'token' => $token,
                'password' => 'yk2<Mw+v/$M61HBI46m5',
                'password_confirmation' => 'yk2<Mw+v/$M61HBI46m5',
            ])
            ->assertOk()
            ->assertJson([
                'message' => 'Het wachtwoord van uw account is gewijzigd.',
            ]);
    }

    public static function notAllowedRolesProvider(): array
    {
        return [
            [ RoleEnum::UserAdmin ]
        ];
    }

    protected function passwordResetRequestResponse(): array
    {
        // @codingStandardsIgnoreStart
        return [
            'message' => 'Indien dit e-mailadres bij ons bekend is, hebben we een e-mail verstuurd met instructies om een nieuw wachtwoord in te stellen.'
        ];
        // @codingStandardsIgnoreEnd
    }
}
