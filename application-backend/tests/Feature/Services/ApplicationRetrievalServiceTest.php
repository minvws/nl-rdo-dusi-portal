<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\Backend\Services\ApplicationRetrievalService;
use MinVWS\DUSi\Application\Backend\Services\EncryptionService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Answer;
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
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

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
    private Answer $answer;
    private string $keyPair;
    private ClientPublicKey $publicKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();

        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->for($subsidy)->create();
        $subsidyStage = SubsidyStage::factory()->for($subsidyVersion)->create();
        $field = Field::factory()->for($subsidyStage)->create();
        $this->identity = Identity::factory()->create();
        $this->application = Application::factory()->for($this->identity)->for($subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($this->application)->for($subsidyStage);
        $this->answer = Answer::factory()->for($applicationStage)->for($field)->create();
        $this->keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($this->keyPair);
        $this->publicKey = new ClientPublicKey($publicKey);
    }

    public function testGetApplicationWithoutDataAndFiles(): void
    {
        $params = new ApplicationParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $this->application->reference,
            false,
            false
        );

        $encryptedResponse = $this->app->get(ApplicationRetrievalService::class)->getApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);

        $this->assertEquals($this->application->reference, $app->reference);
        $this->assertNull($app->data);
        $this->assertNull($app->files);
    }

    public function testGetApplicationWithDataAndFiles(): void
    {
        $params = new ApplicationParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $this->application->reference,
            true,
            true
        );

        $encryptedResponse = $this->app->get(ApplicationRetrievalService::class)->getApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);

        $this->assertEquals($this->application->reference, $app->reference);
        $this->assertNotNull($app->data);
        $this->assertObjectHasProperty($this->answer->field->code, $app->data);
        $answerValue = json_decode($encryptionService->decryptBase64EncodedData($this->answer->encrypted_answer));
        $this->assertEquals($answerValue, $app->data->{$this->answer->field->code});
        $this->assertIsArray($app->files);
    }

    public static function useRealIdentityProvider(): array
    {
        return [
            [false, 'identity_not_found'],
            [true, 'application_not_found']
        ];
    }

    /**
     * @dataProvider useRealIdentityProvider
     */
    public function testGetApplicationReturnsNotFound(bool $useRealIdentity, string $expectedErrorCode): void
    {
        $identity = $useRealIdentity ? Identity::factory()->create() : null;

        $params = new ApplicationParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: $identity?->hashed_identifier ?? $this->faker->uuid
            ),
            $this->publicKey,
            $this->application->reference,
            false,
            false
        );

        $encryptedResponse = $this->app->get(ApplicationRetrievalService::class)->getApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $error = $encryptionService->decryptCodableResponse($encryptedResponse, Error::class, $this->keyPair);
        $this->assertEquals($expectedErrorCode, $error->code);
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
        $list = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationList::class, $this->keyPair);
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
        $list = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationList::class, $this->keyPair);
        $this->assertNotNull($list);
        $this->assertCount(0, $list->items);
    }
}
