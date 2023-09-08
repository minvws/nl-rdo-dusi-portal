<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\Backend\Services\ApplicationRetrievalService;
use MinVWS\DUSi\Application\Backend\Services\EncryptionService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use OpenSSLAsymmetricKey;

/**
 * @group application
 * @group application-retrieval-service
 */
class ApplicationRetrievalServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use MocksEncryptionAndHashing;

    private Identity $identity;
    private Application $application;
    private OpenSSLAsymmetricKey $privateKey;
    private ClientPublicKey $publicKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();

        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->for($subsidy)->create();
        $subsidyStage = SubsidyStage::factory()->for($subsidyVersion)->create();
        $this->identity = Identity::factory()->create();
        $this->application = Application::factory()->for($this->identity)->for($subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($this->application)->for($subsidyStage);

        $key = openssl_pkey_new();
        $this->assertNotFalse($key);
        $this->privateKey = $key;
        $publicKey = openssl_pkey_get_details($key)['key'];
        $this->assertNotFalse($publicKey);
        $publicKey = (string)$publicKey;
        $this->publicKey = new ClientPublicKey($publicKey);
    }

    public function testGetApplication(): void
    {
        $params = new ApplicationParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $this->application->reference,
            false
        );

        $encryptedResponse = $this->app->get(ApplicationRetrievalService::class)->getApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptResponse($encryptedResponse, $this->privateKey, ApplicationDTO::class);
        $this->assertNotNull($app);

        $this->assertEquals($this->application->reference, $app->reference);
    }

    public static function useRealIdentityProvider(): array
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @dataProvider useRealIdentityProvider
     */
    public function testGetApplicationReturnsNotFound(bool $useRealIdentity): void
    {
        $identity = $useRealIdentity ? Identity::factory()->create() : null;

        $params = new ApplicationParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: $identity?->hashed_identifier ?? $this->faker->uuid
            ),
            $this->publicKey,
            $this->application->reference,
            false
        );

        $encryptedResponse = $this->app->get(ApplicationRetrievalService::class)->getApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptResponse($encryptedResponse, $this->privateKey, ApplicationDTO::class);
        $this->assertNull($app);
    }

    public function testListApplications(): void
    {
        $params = new ApplicationListParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey
        );

        $encryptedResponse = $this->app->get(ApplicationRetrievalService::class)->listApplications($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $list = $encryptionService->decryptResponse($encryptedResponse, $this->privateKey, ApplicationList::class);
        $this->assertNotNull($list);
        $this->assertCount(1, $list->items);
        $this->assertEquals($this->application->reference, $list->items[0]->reference);
    }

    /**
     * @dataProvider useRealIdentityProvider
     */
    public function testListApplicationsReturnsEmptyList(bool $useRealIdentity): void
    {
        $identity = $useRealIdentity ? Identity::factory()->create() : null;

        $params = new ApplicationListParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: $identity?->hashed_identifier ?? $this->faker->uuid
            ),
            $this->publicKey
        );

        $encryptedResponse = $this->app->get(ApplicationRetrievalService::class)->listApplications($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $list = $encryptionService->decryptResponse($encryptedResponse, $this->privateKey, ApplicationList::class);
        $this->assertNotNull($list);
        $this->assertCount(0, $list->items);
    }
}
