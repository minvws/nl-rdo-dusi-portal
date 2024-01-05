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

    /**
     * @dataProvider passwordPolicyProvider
     * @param string $newPassword
     * @param bool $valid
     * @return void
     */
    public function testUserCanResetPasswordWithTokenPasswordPolicy(
        string $newPassword,
        bool $valid,
        ?array $validationMessages = null
    ): void {
        // Create assessor user
        $user = User::factory()->create([
            'name' => 'New User',
            'password' => Hash::make('password'),
        ]);
        $user->attachRole(RoleEnum::Assessor);

        // Create token
        $token = $this->resetTokenRepository->create($user);

        // Password reset request
        $response = $this
            ->json('POST', route('password.update'), [
                'email' => $user->email,
                'token' => $token,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        if ($valid) {
            $response
                ->assertOk()
                ->assertJson([
                    'message' => 'Het wachtwoord van uw account is gewijzigd.',
                ]);
        } else {
            $response
                ->assertStatus(422)
                ->assertJson($validationMessages, true);
        }
    }

    public static function passwordPolicyProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength
        return [
            [ 'aaaaaaaaaaaaaaa', false, [
                'message' => 'Wachtwoord moet minimaal één kleine letter en één hoofdletter bevatten. (en 2 andere foutmeldingen)',
                "errors" => [
                    "password" => [
                        'Wachtwoord moet minimaal één kleine letter en één hoofdletter bevatten.',
                        'Wachtwoord moet minimaal één vreemd teken bevatten.',
                        'Wachtwoord moet minimaal één cijfer bevatten.',
                    ],
                ],
            ] ],
            [ 'aaaaaaaBBBBBBBB', false, [
                'message' => 'Wachtwoord moet minimaal één vreemd teken bevatten. (en 1 andere foutmelding)',
                "errors" => [
                    "password" => [
                        'Wachtwoord moet minimaal één vreemd teken bevatten.',
                        'Wachtwoord moet minimaal één cijfer bevatten.',
                    ],
                ],
            ] ],
            [ '42aaaaaaaBBBBBBBB', false, [
                'message' => 'Wachtwoord moet minimaal één vreemd teken bevatten.',
                "errors" => [
                    "password" => [
                        'Wachtwoord moet minimaal één vreemd teken bevatten.',
                    ],
                ],
            ] ],
            [ '@aaaaaaBBBBBBBB', false, [
                'message' => 'Wachtwoord moet minimaal één cijfer bevatten.',
                "errors" => [
                    "password" => [
                        'Wachtwoord moet minimaal één cijfer bevatten.',
                    ],
                ],
            ] ],
            [ '42@aaaaBBBBBBBB', true ],
            [ '42@BBBBBBBBBBBB', false, [
                'message' => 'Wachtwoord moet minimaal één kleine letter en één hoofdletter bevatten.',
                "errors" => [
                    "password" => ['Wachtwoord moet minimaal één kleine letter en één hoofdletter bevatten.',
                    ],
                ],
            ] ],
            [ '42@aBBBBBBBBBBB', true ],
        ];
        // phpcs:enable Generic.Files.LineLength
    }

    public static function notAllowedRolesProvider(): array
    {
        return [
            [ RoleEnum::UserAdmin ]
        ];
    }

    protected function passwordResetRequestResponse(): array
    {
        // phpcs:disable Generic.Files.LineLength
        return [
            'message' => 'Indien dit e-mailadres bij ons bekend is, hebben we een e-mail verstuurd met instructies om een nieuw wachtwoord in te stellen.'
        ];
        // phpcs:enable Generic.Files.LineLength
    }
}
