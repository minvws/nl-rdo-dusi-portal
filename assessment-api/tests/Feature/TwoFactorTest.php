<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\User;
use PragmaRX\Google2FA\Google2FA;
use MinVWS\DUSi\Shared\Application\Models\Connection as ApplicationConnection;
use MinVWS\DUSi\Shared\User\Models\Connection as UserConnection;

class TwoFactorTest extends TestCase
{
    use DatabaseTransactions;

    protected array $connectionsToTransact = [ApplicationConnection::APPLICATION, UserConnection::USER];
    private Application $application;
    private ApplicationStage $applicationStage;

    protected function setUp(): void
    {
        parent::setUp();
        $authProvider = app(TwoFactorAuthenticationProvider::class);

        $user = User::updateOrCreate([
            'email' => 'user@example.com'
        ], [
            'name' => 'New User',
            'password' => Hash::make('password'),
            'organisation_id' => Organisation::factory()->create()?->id,
        ]);
        $user->two_factor_secret = encrypt($authProvider->generateSecretKey());
        $user->save();

        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy->id,
        ]);
        $this->application = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
            ]
        );
        $this->applicationStage = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application->id,
            ]
        );
    }

    public function testTwoFactor()
    {
        $response = $this->json('POST', '/api/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);
        $this->assertEquals('{"two_factor":true}', $response->getContent());

        $response = $this->json('POST', '/api/two-factor-challenge', [
            'code' => '123456',
        ]); // check if message contains "The provided code was invalid."

        $this->assertContains(
            "The provided two factor authentication code was invalid.",
            json_decode($response->getContent(), true)
        );

        $response = $this->json('GET', '/api/applications');
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals("Unauthenticated.", $data["message"]); // check if message contains "Unauthenticated."

        $this->withoutMiddleware(); // disable authentication and test again

        $response = $this->json('GET', '/api/applications');
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($this->application->id, $data['data'][0]['id']);
    }

    public function testLogin(): void
    {
        $engine = new Google2FA();

        $user = User::factory()
            ->withPassword('password')
            ->withTwoFactorSecret($engine->generateSecretKey())
            ->create();

        $response = $this
            ->postJson('/api/login', [
                'email'    => $user->email,
                'password' => 'password',
            ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'two_factor' => true,
            ]);

        $response = $this
            ->postJson('/api/two-factor-challenge', [
                'code' => $engine->getCurrentOtp(decrypt($user->two_factor_secret))
            ]);

        $response->assertStatus(204);
    }
}
