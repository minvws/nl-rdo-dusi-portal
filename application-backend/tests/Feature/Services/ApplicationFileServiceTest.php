<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use MinVWS\DUSi\Application\Backend\Services\ApplicationFileService;
use MinVWS\DUSi\Application\Backend\Services\ResponseEncryptionService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUploadResult;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Ramsey\Uuid\Uuid;

/**
 * @group application
 * @group application-file-service
 */
class ApplicationFileServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use MocksEncryptionAndHashing;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage;
    private Application $application;
    private ApplicationStage $applicationStage;
    private Identity $identity;
    private Field $field;
    private string $keyPair;
    private ClientPublicKey $publicKey;
    private Filesystem $fileSystem;
    private ResponseEncryptionService $responseEncryptionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();
        $this->withoutFrontendEncryption();

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion =
            SubsidyVersion::factory()
                ->for($this->subsidy)
                ->create(['status' => VersionStatus::Published]);
        $this->subsidyStage = SubsidyStage::factory()->for($this->subsidyVersion)->create();
        $this->field = Field::factory()->for($this->subsidyStage)->create(['type' => FieldType::Upload]);

        $this->identity = Identity::factory()->create();
        $this->application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $this->applicationStage =
            ApplicationStage::factory()
                ->for($this->application)
                ->for($this->subsidyStage)
                ->create();

        $this->keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($this->keyPair);
        $this->publicKey = new ClientPublicKey($publicKey);

        Storage::fake(Disk::APPLICATION_FILES);

        $this->responseEncryptionService = $this->app->make(ResponseEncryptionService::class);
    }

    public function testApplicationFileUpload(?BinaryData $data = null): string
    {
        $params = new EncryptedApplicationFileUploadParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->application->reference,
            $this->field->code,
            $data ?? new BinaryData(file_get_contents(__DIR__ . '/../../fixtures/test.pdf'))
        );

        $encryptedResponse = $this->app->get(ApplicationFileService::class)->saveApplicationFile($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::CREATED, $encryptedResponse->status);

        $result = $this->responseEncryptionService->decryptCodable(
            $encryptedResponse,
            FileUploadResult::class,
            $this->keyPair
        );
        $this->assertNotNull($result);
        $this->assertTrue(Uuid::isValid($result->id));

        $fileRepository = $this->app->get(ApplicationFileRepository::class);
        $this->assertTrue($fileRepository->fileExists($this->applicationStage, $this->field, $result->id));
        $this->assertEquals(
            $params->data->data,
            $fileRepository->readFile($this->applicationStage, $this->field, $result->id)
        ); // this only works because encryption is mocked

        return $result->id;
    }

    public function testApplicationFileDownload(): void
    {
        $data = new BinaryData('content');
        $fileId = $this->testApplicationFileUpload($data);

        $params = new ApplicationFileParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->application->reference,
            $this->field->code,
            $fileId
        );

        $encryptedResponse = $this->app->get(ApplicationFileService::class)->getApplicationFile($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);
        $this->assertEquals('text/plain', $encryptedResponse->contentType);

        $decryptedResponse = $this->responseEncryptionService->decrypt(
            $encryptedResponse,
            $this->keyPair
        );
        $this->assertEquals($data->data, $decryptedResponse);
    }
}
