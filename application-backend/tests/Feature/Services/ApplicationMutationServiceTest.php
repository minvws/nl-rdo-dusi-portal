<?php

declare(strict_types=1);

namespace Feature\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\ApplicationMutationService;
use MinVWS\DUSi\Application\Backend\Services\EncryptionService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFindOrCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Ramsey\Uuid\Uuid;
use Storage;

/**
 * @group application
 * @group application-mutation-service
 */
class ApplicationMutationServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use MocksEncryptionAndHashing;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage;
    private Identity $identity;
    private Field $textField;
    private Field $uploadField;
    private string $keyPair;
    private ClientPublicKey $publicKey;

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
        $this->textField =
            Field::factory()
                ->for($this->subsidyStage)
                ->create(['code' => 'text']);
        $this->uploadField =
            Field::factory()
                ->for($this->subsidyStage)
                ->create(['code' => 'file', 'type' => FieldType::Upload, 'is_required' => false]);

        $this->identity = Identity::factory()->create();

        $this->keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($this->keyPair);
        $this->publicKey = new ClientPublicKey($publicKey);

        Storage::fake(Disk::APPLICATION_FILES);
    }

    public function testFindOrCreateApplicationWhenNoApplicationExists(): void
    {
        $params = new ApplicationFindOrCreateParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->findOrCreateApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::CREATED, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertNotNull($app->data);
        $this->assertCount(0, get_object_vars($app->data));
        $this->assertEquals($this->subsidy->code, $app->subsidy->code);
        $this->assertEquals(ApplicationStatus::Draft, $app->status);
    }

    public function testFindOrCreateApplicationReturnsExistingDraftApplication(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage);
        $answer = Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $params = new ApplicationFindOrCreateParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->findOrCreateApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertEquals($application->reference, $app->reference);
        $this->assertNotNull($app->data);
        $this->assertCount(1, get_object_vars($app->data));
        $this->assertObjectHasProperty($this->textField->code, $app->data);
        $answerValue = json_decode($encryptionService->decryptBase64EncodedData($answer->encrypted_answer));
        $this->assertEquals($answerValue, $app->data->{$this->textField->code});
        $this->assertEquals(ApplicationStatus::Draft, $app->status);
    }

    public function testFindOrCreateApplicationCreatesNewApplicationWhenOnlyARejectedApplicationExists(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create([
            'status' => ApplicationStatus::Rejected
        ]);
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $params = new ApplicationFindOrCreateParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->findOrCreateApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::CREATED, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertNotNull($app->data);
        $this->assertCount(0, get_object_vars($app->data));
        $this->assertEquals($this->subsidy->code, $app->subsidy->code);
        $this->assertEquals(ApplicationStatus::Draft, $app->status);
    }

    public function testFindOrCreateApplicationReturnsErrorIfApplicationAlreadyExistsAndIsNotEditable(): void
    {
        $application =
            Application::factory()
                ->for($this->identity)
                ->for($this->subsidyVersion)
                ->create(['status' => ApplicationStatus::Submitted]);
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $params = new ApplicationFindOrCreateParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->findOrCreateApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::FORBIDDEN, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $error = $encryptionService->decryptCodableResponse($encryptedResponse, Error::class, $this->keyPair);
        $this->assertEquals('application_already_exists', $error->code);
    }

    public static function saveApplicationDataProvider(): array
    {
        return [
            [false, ApplicationStatus::Draft],
            [true, ApplicationStatus::Submitted]
        ];
    }

    /**
     * @dataProvider saveApplicationDataProvider
     */
    public function testSaveApplication(bool $submit, ApplicationStatus $expectedStatus): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $body = new ApplicationSaveBody(
            (object)[$this->textField->code => $this->faker->text],
            $submit
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertEquals($application->reference, $app->reference);
        $this->assertNotNull($app->data);
        $this->assertCount(1, get_object_vars($app->data));
        $this->assertObjectHasProperty($this->textField->code, $app->data);
        $this->assertEquals($body->data->{$this->textField->code}, $app->data->{$this->textField->code});
        $this->assertEquals($expectedStatus, $app->status);
    }

    public static function saveApplicationOnlyAllowedForEditableStatusesProvider(): array
    {
        return array_map(
            fn (ApplicationStatus $s) =>
                [$s, $s->isEditableForApplicant() ? EncryptedResponseStatus::OK : EncryptedResponseStatus::FORBIDDEN],
            ApplicationStatus::cases()
        );
    }

    /**
     * @dataProvider saveApplicationOnlyAllowedForEditableStatusesProvider
     */
    public function testSaveApplicationOnlyAllowedForEditableStatuses(
        ApplicationStatus $status,
        EncryptedResponseStatus $expectedResponseStatus
    ): void {
        $application =
            Application::factory()
                ->for($this->identity)
                ->for($this->subsidyVersion)
                ->create(['status' => $status]);
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $body = new ApplicationSaveBody(
            (object)[$this->textField->code => $this->faker->text],
            true
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals($expectedResponseStatus, $encryptedResponse->status);
    }


    /**
     * @dataProvider saveApplicationDataProvider
     */
    public function testSaveApplicationWithFile(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage)->create();
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $fileId = Uuid::uuid4()->toString();

        $fileRepository = $this->app->get(ApplicationFileRepository::class);
        $fileRepository->writeFile($applicationStage, $this->uploadField, $fileId, random_bytes(100));

        $body = new ApplicationSaveBody(
            (object)[
                $this->textField->code => $this->faker->text,
                $this->uploadField->code => [(object)[
                    'id' => $fileId,
                    'name' => 'filename.pdf',
                    'mimeType' => 'application/pdf'
                ]]
            ],
            false
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $encryptionService = $this->app->get(EncryptionService::class);
        $app = $encryptionService->decryptCodableResponse($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertEquals($application->reference, $app->reference);
        $this->assertNotNull($app->data);
        $this->assertCount(2, get_object_vars($app->data));
        $this->assertObjectHasProperty($this->uploadField->code, $app->data);
        $this->assertEquals($body->data->{$this->uploadField->code}, $app->data->{$this->uploadField->code});
    }

    /**
     * @dataProvider saveApplicationDataProvider
     */
    public function testSaveApplicationWithMissingFile(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage)->create();
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $fileId = Uuid::uuid4()->toString();

        $body = new ApplicationSaveBody(
            (object)[
                $this->textField->code => $this->faker->text,
                $this->uploadField->code => [(object)[
                    'id' => $fileId,
                    'name' => 'filename.pdf',
                    'mimeType' => 'application/pdf'
                ]]
            ],
            false
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(IdentityType::CitizenServiceNumber, $this->identity->hashed_identifier),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        // TODO: should be a validation error e.g. EncryptedResponseStatus::BAD_REQUEST
        $this->assertEquals(EncryptedResponseStatus::INTERNAL_SERVER_ERROR, $encryptedResponse->status);
    }
}
