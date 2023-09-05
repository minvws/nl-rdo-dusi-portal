<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use MinVWS\DUSi\Assessment\API\Models\Connection;
use MinVWS\DUSi\Assessment\API\Models\User;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class TwoFactorTest extends TestCase
{
    use DatabaseTransactions;

    protected array $connectionsToTransact = [Connection::APPLICATION, Connection::USER];
    private Application $application;
    private ApplicationStage $applicationStage;

    protected function setUp(): void
    {
        parent::setUp();
        $authProvider = app(TwoFactorAuthenticationProvider::class);

        $user = User::query()->where('email', 'user@example.com')->first();

        if (!$user) {
            $newUser = new User();
            $newUser->name = 'New User';
            $newUser->email = 'user@example.com';
            $newUser->password = \Hash::make('password');
            $newUser->two_factor_secret = encrypt($authProvider->generateSecretKey());
            $newUser->save();

            echo "User created successfully.";
        } else {
            echo "User already exists.";
        }

        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();
        ApplicationStageVersion::query()->truncate();

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
        $this->applicationStageVersion = ApplicationStageVersion::factory()->create(
            [
                'application_stage_id' => $this->applicationStage->id,
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
}
