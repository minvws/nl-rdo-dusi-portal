<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Application\API\Models\PortalUser;
use MinVWS\DUSi\Application\API\Services\ApplicationService;
use MinVWS\DUSi\Application\API\Services\CacheService;
use MinVWS\DUSi\Application\API\Services\Exceptions\DataEncryptionException;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Application\API\Services\SubsidyStageService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFileUpload;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageUI;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Ramsey\Uuid\Uuid;
use MinVWS\DUSi\Application\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

/**
 * @group application
 * @group application-controller
 */
class ApplicationControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::APPLICATION];

    private PortalUser $user;
    private Subsidy $subsidy;
    private SubsidyStage $subsidyStage;
    private Field $field;
    private SubsidyStageUI $formUI;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();

        $this->user = new PortalUser(
            base64_encode(openssl_random_pseudo_bytes(32)),
            Uuid::uuid4()->toString(),
            null
        );
        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()->create(
            [
                'subsidy_id' => $this->subsidy->id,
                'status' => VersionStatus::Published,
                'version' => 1,
                'subsidy_page_url' => 'https://dus-i.nl',
                'contact_mail_address' => 'dienstpostbus@minvws.nl',
                'mail_to_address_field_identifier' => 'email',
                'mail_to_name_field_identifier' => 'firstName;infix;lastName',
                'message_overview_subject' => 'Onderwerp test',
            ]
        );
        $this->subsidyStage = SubsidyStage::factory()->create(
            ['subsidy_version_id' => $this->subsidyVersion->id]
        );
        $this->field = Field::factory()->for($this->subsidyStage)->create();
        $this->ui = SubsidyStageUI::factory()->create([
            'subsidy_stage_id' => $this->subsidyStage->id,
            'status' => VersionStatus::Published
        ]);
        $this->app->get(CacheService::class)->cacheSubsidyStage($this->subsidyStage);
    }

    public function testCreateDraft(): void
    {
        $this->setKeyPair();
        $response = $this
            ->be($this->user)
            ->postJson(route('api.application-create-draft', ['form' => $this->subsidyStage->id]));
        $this->assertEquals(202, $response->status());
        $applicationId = $response->json('id');
        $this->assertIsString($applicationId);
        $application = $this->app->get(StateService::class)->getDraftApplication($applicationId);
        $this->assertNotNull($application);
        $this->assertEquals($applicationId, $application->id);
        $this->assertEquals($this->subsidyStage->id, $application->formId);
    }

    protected function setKeyPair(): string
    {
        $config = array(
            "private_key_bits" => 2048,  // Size of the private key in bits
            "private_key_type" => OPENSSL_KEYTYPE_RSA,  // Type of key
        );

        // Create the private and public key
        $privateKey = openssl_pkey_new($config);

        // Extract the private key from the pair
        openssl_pkey_export($privateKey, $privateKeyString);

        // Get the public key
        $publicKeyDetails = openssl_pkey_get_details($privateKey);
        $publicKey = $publicKeyDetails['key'];

        $publicKeyFilePath = env("HSM_PUBLIC_KEY_FILE_PATH");

        // Write the public key to the specified path
        file_put_contents($publicKeyFilePath, $publicKey);

        return $privateKeyString;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws DataEncryptionException
     * @throws ContainerExceptionInterface
     */
    public function testFileUpload(): void
    {
        $privateKey = $this->setKeyPair();
        Queue::fake();

        $user = new PortalUser(
            base64_encode(openssl_random_pseudo_bytes(32)),
            Uuid::uuid4()->toString(),
            null
        );

        $cachedForm = $this->app->get(SubsidyStageService::class)->getSubsidyStage($this->subsidyStage->id);
        $applicationId = $this->app->get(ApplicationService::class)->createDraft($cachedForm);
        $fakeFile = UploadedFile::fake()
            ->createWithContent('id.pdf', 'This should be an encrypted string');
        $data = ['fieldCode' => $this->field->code, 'file' => $fakeFile];
        $response = $this->actingAs($user)->postJson(
            route('api.application-upload-file', ['application' => $applicationId]),
            $data
        );
        $this->assertEquals(202, $response->status());

        $fileId = $response->json('id');
        $this->assertIsString($fileId);

        Queue::assertPushed(ProcessFileUpload::class);
        $jobs = Queue::pushed(ProcessFileUpload::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertInstanceOf(ProcessFileUpload::class, $job);
        $this->assertEquals($fileId, $job->fileUpload->id);
        $this->assertEquals(IdentityType::CitizenServiceNumber, $job->fileUpload->identity->type);
        $this->assertEquals($applicationId, $job->fileUpload->applicationMetadata->applicationId);
        $this->assertEquals($this->subsidyStage->id, $job->fileUpload->applicationMetadata->subsidyStageId);
        $this->assertEquals($this->field->code, $job->fileUpload->fieldCode);
        $this->assertEquals('pdf', $job->fileUpload->extension);
        $this->assertEquals('application/pdf', $job->fileUpload->mimeType);



        $this->assertEquals(
            base64_encode($fakeFile->getContent()),
            $this->decryptData($job->fileUpload->encryptedContents, $privateKey)
        );
    }

    public function testFileUploadRequiresLogin(): void
    {
        Queue::fake();

        $cachedForm = $this->app->get(SubsidyStageService::class)->getSubsidyStage($this->subsidyStage->id);
        $applicationId = $this->app->get(ApplicationService::class)->createDraft($cachedForm);
        $fakeFile = UploadedFile::fake()->createWithContent('id.pdf', 'This should be an encrypted string');
        $data = ['fieldCode' => $this->field->code, 'file' => $fakeFile];
        $response = $this->postJson(route('api.application-upload-file', ['application' => $applicationId]), $data);
        $this->assertEquals(401, $response->status());

        Queue::assertNotPushed(ProcessFileUpload::class);
    }

    public function testSubmit(): void
    {
        Queue::fake();

        $privateKey = $this->setKeyPair();

        $user = new PortalUser(
            base64_encode(openssl_random_pseudo_bytes(32)),
            Uuid::uuid4()->toString(),
            null
        );

        $cachedForm = $this->app->get(SubsidyStageService::class)->getSubsidyStage($this->subsidyStage->id);
        $applicationId = $this->app->get(ApplicationService::class)->createDraft($cachedForm);
        $data['data'] = 'This should be an encrypted string';
        $response = $this->actingAs($user)
            ->putJson(route('api.application-submit', ['application' => $applicationId]), $data);
        $this->assertEquals(202, $response->status());

        Queue::assertPushed(ProcessFormSubmit::class);
        $jobs = Queue::pushed(ProcessFormSubmit::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertInstanceOf(ProcessFormSubmit::class, $job);
        $this->assertEquals(IdentityType::CitizenServiceNumber, $job->formSubmit->identity->type);
        $this->assertEquals($applicationId, $job->formSubmit->applicationMetadata->applicationId);
        $this->assertEquals($this->subsidyStage->id, $job->formSubmit->applicationMetadata->subsidyStageId);



        $this->assertEquals(
            base64_encode($data['data']),
            $this->decryptData($job->formSubmit->encryptedData, $privateKey)
        );
    }

    protected function decryptData(string $encryptedContents, string $privateKey): string
    {
        openssl_private_decrypt(
            base64_decode(json_decode(
                base64_decode($encryptedContents),
                true
            )['encrypted_aes']),
            $decryptedAes,
            $privateKey,
            OPENSSL_PKCS1_OAEP_PADDING
        );

        return base64_encode(openssl_decrypt(
            base64_decode(json_decode(base64_decode($encryptedContents), true)['encrypted']),
            'AES-256-CBC',
            $decryptedAes,
            OPENSSL_RAW_DATA,
            base64_decode(json_decode(base64_decode($encryptedContents), true)['iv'])
        ));
    }

    public function testSubmitRequiresLogin(): void
    {
        Queue::fake();

        $cachedForm = $this->app->get(SubsidyStageService::class)->getSubsidyStage($this->subsidyStage->id);
        $applicationId = $this->app->get(ApplicationService::class)->createDraft($cachedForm);
        $data['data'] = 'This should be an encrypted string';
        $response = $this->putJson(route('api.application-submit', ['application' => $applicationId]), $data);
        $this->assertEquals(401, $response->status());

        Queue::assertNotPushed(ProcessFormSubmit::class);
    }
}
