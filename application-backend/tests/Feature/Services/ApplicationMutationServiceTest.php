<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Services\ApplicationMutationService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\SurePayValidationRule;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ValidationResultDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Ramsey\Uuid\Uuid;

/**
 * @group application
 * @group application-mutation-service
 */
class ApplicationMutationServiceTest extends TestCase
{
    use WithFaker;
    use MocksEncryptionAndHashing;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStage $subsidyStage2;
    private Identity $identity;
    private Field $textField;
    private Field $uploadField;
    private Field $bankAccountHolderField;
    private Field $bankAccountNumberField;
    private Field $bankStatementField;
    private string $keyPair;
    private ClientPublicKey $publicKey;
    private ResponseEncryptionService $responseEncryptionService;
    private ApplicationMutationService $applicationMutationService;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->withoutFrontendEncryption();

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion =
            SubsidyVersion::factory()
                ->for($this->subsidy)
                ->create(['status' => VersionStatus::Published]);
        $this->subsidyStage1 = SubsidyStage::factory()->for($this->subsidyVersion)->create();
        $this->textField =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create(['code' => 'text']);
        $this->uploadField =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create(['code' => 'file', 'type' => FieldType::Upload, 'is_required' => false]);
        $this->bankAccountHolderField =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                    'code' => SurePayValidationRule::BANK_ACCOUNT_HOLDER_FIELD,
                    'type' => FieldType::Text,
                    'is_required' => false,
                ]);
        $this->bankAccountNumberField =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                    'code' => SurePayValidationRule::BANK_ACCOUNT_NUMBER_FIELD,
                    'type' => FieldType::CustomBankAccount,
                    'is_required' => false,
                ]);
        $this->bankStatementField =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                    'code' => SurePayValidationRule::BANK_STATEMENT_FIELD,
                    'type' => FieldType::Upload,
                    'is_required' => false,
                ]);
        $this->subsidyStage2 =
            SubsidyStage::factory()
                ->for($this->subsidyVersion)
                ->create(['stage' => 2, 'subject_role' => SubjectRole::Assessor]);
        SubsidyStageTransition::factory()
            ->for($this->subsidyStage1, 'currentSubsidyStage')
            ->for($this->subsidyStage2, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Pending]);

        $this->identity = Identity::factory()->create();

        $this->keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($this->keyPair);
        $this->publicKey = new ClientPublicKey($publicKey);

        Storage::fake(Disk::APPLICATION_FILES);

        $this->responseEncryptionService = $this->app->make(ResponseEncryptionService::class);
        $this->applicationMutationService = $this->app->make(ApplicationMutationService::class);
    }

    public function testCreateApplicationWhenNoApplicationExists(): void
    {
        $params = new ApplicationCreateParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->applicationMutationService->createApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::CREATED, $encryptedResponse->status);

        $app = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertNotNull($app->data);
        $this->assertCount(0, get_object_vars($app->data));
        $this->assertEquals($this->subsidy->code, $app->subsidy->code);
        $this->assertEquals(ApplicationStatus::Draft, $app->status);
        $this->assertDatabaseHas('application_references', ['reference' => $app->reference]);
    }

    public function testCreateApplicationForbiddenWhenOpenApplicationExists(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1);
        $answer = Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $params = new ApplicationCreateParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->applicationMutationService->createApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::FORBIDDEN, $encryptedResponse->status);


        $error = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, Error::class, $this->keyPair);
        $this->assertNotNull($error);

        $this->assertEquals('subsidy_does_not_allow_multiple_applications', $error->code);
    }

    public function testCreateApplicationCreatesNewApplicationWhenOnlyARejectedApplicationExists(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create([
            'status' => ApplicationStatus::Rejected
        ]);
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $params = new ApplicationCreateParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, ''),
            ),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->applicationMutationService->createApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::CREATED, $encryptedResponse->status);

        $app = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertNotNull($app->data);
        $this->assertCount(0, get_object_vars($app->data));
        $this->assertEquals($this->subsidy->code, $app->subsidy->code);
        $this->assertEquals(ApplicationStatus::Draft, $app->status);
        $this->assertDatabaseHas('application_references', ['reference' => $app->reference]);
    }

    public function testCreateApplicationReturnsNewApplicationWhenSubsidyAllowsMultipleApplications(): void
    {
        $this->subsidy->allow_multiple_applications = true;
        $this->subsidy->save();

        $application =
            Application::factory()
                ->for($this->identity)
                ->for($this->subsidyVersion)
                ->create(['status' => ApplicationStatus::Pending]);
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $params = new ApplicationCreateParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->applicationMutationService->createApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::CREATED, $encryptedResponse->status);

        $app = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertNotNull($app->data);
        $this->assertCount(0, get_object_vars($app->data));
        $this->assertEquals($this->subsidy->code, $app->subsidy->code);
        $this->assertEquals(ApplicationStatus::Draft, $app->status);
        $this->assertDatabaseHas('application_references', ['reference' => $app->reference]);
    }

    public static function createApplicationChecksIfSubsidyOpenForNewApplicationsProvider(): array
    {
        return [
            'no_existing_application_should_return_error' => [
                null,
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_closed_for_new_applications'
            ],
            'existing_draft_application_should_return_error' => [
                ApplicationStatus::Draft,
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_does_not_allow_multiple_applications'
            ],
            'existing_request_for_changes_application_should_return_an_error' => [
                ApplicationStatus::RequestForChanges,
                EncryptedResponseStatus::FORBIDDEN,
                'subsidy_does_not_allow_multiple_applications'
            ]
        ];
    }

    /**
     * @dataProvider createApplicationChecksIfSubsidyOpenForNewApplicationsProvider
     */
    public function testCreateApplicationChecksIfSubsidyOpenForNewApplications(
        ?ApplicationStatus $existingApplicationStatus,
        EncryptedResponseStatus $expectedResponseStatus,
        ?string $expectedErrorCode
    ): void {
        $now = CarbonImmutable::instance($this->subsidy->valid_to)->addDay();
        Carbon::setTestNow($now);
        CarbonImmutable::setTestNow($now);

        if ($existingApplicationStatus) {
            $application =
                Application::factory()
                    ->for($this->identity)
                    ->for($this->subsidyVersion)
                    ->create(['status' => $existingApplicationStatus]);
            $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1);
            Answer::factory()->for($applicationStage)->for($this->textField)->create();
        }

        $params = new ApplicationCreateParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->applicationMutationService->createApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals($expectedResponseStatus, $encryptedResponse->status);

        if ($expectedErrorCode) {
            $error = $this->responseEncryptionService
                ->decryptCodable($encryptedResponse, Error::class, $this->keyPair);
            $this->assertEquals($expectedErrorCode, $error->code);
        }
    }

    public static function saveApplicationDataProvider(): array
    {
        return [
            [false, ApplicationStatus::Draft],
            [true, ApplicationStatus::Pending]
        ];
    }

    /**
     * @dataProvider saveApplicationDataProvider
     * @group validation
     */
    public function testSaveApplication(bool $submit, ApplicationStatus $expectedStatus): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $body = new ApplicationSaveBody(
            (object)[$this->textField->code => $this->faker->text],
            $submit
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->applicationMutationService->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $app = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, ApplicationDTO::class, $this->keyPair);
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
     * @group validation
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
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $body = new ApplicationSaveBody(
            (object)[$this->textField->code => $this->faker->text],
            true
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->applicationMutationService->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals($expectedResponseStatus, $encryptedResponse->status);
    }

    public static function saveApplicationChecksIfSubsidyOpenForNewApplicationsProvider(): array
    {
        return [
            'draft_cannot_be_saved' => [
                ApplicationStatus::Draft,
                false,
                EncryptedResponseStatus::NOT_FOUND,
                'application_not_found'
            ],
            'draft_cannot_be_submitted' => [
                ApplicationStatus::Draft,
                true,
                EncryptedResponseStatus::NOT_FOUND,
                'application_not_found'
            ],
            'request_for_changes_can_be_saved' =>
                [ApplicationStatus::RequestForChanges, false, EncryptedResponseStatus::OK, null],
            'request_for_changes_can_be_submitted' =>
                [ApplicationStatus::RequestForChanges, true, EncryptedResponseStatus::OK, null]
        ];
    }

    /**
     * @dataProvider saveApplicationChecksIfSubsidyOpenForNewApplicationsProvider
     */
    public function testSaveApplicationChecksIfSubsidyOpenForNewApplications(
        ApplicationStatus $status,
        bool $submit,
        EncryptedResponseStatus $expectedResponseStatus,
        ?string $expectedErrorCode
    ): void {
        $now = CarbonImmutable::instance($this->subsidy->valid_to)->addDay();
        Carbon::setTestNow($now);
        CarbonImmutable::setTestNow($now);

        $application =
            Application::factory()
                ->for($this->identity)
                ->for($this->subsidyVersion)
                ->create(['status' => $status]);
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1);
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $body = new ApplicationSaveBody(
            (object)[$this->textField->code => $this->faker->text],
            $submit
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->applicationMutationService->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals($expectedResponseStatus, $encryptedResponse->status);

        if ($expectedErrorCode) {
            $error = $this->responseEncryptionService
                ->decryptCodable($encryptedResponse, Error::class, $this->keyPair);
            $this->assertEquals($expectedErrorCode, $error->code);
        }
    }


    /**
     * @dataProvider saveApplicationDataProvider
     */
    public function testSaveApplicationWithFile(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        $fileId = Uuid::uuid4()->toString();

        $fileRepository = $this->app->get(ApplicationFileManager::class);
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
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->applicationMutationService->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $app = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, ApplicationDTO::class, $this->keyPair);
        $this->assertNotNull($app);
        $this->assertEquals($application->reference, $app->reference);
        $this->assertNotNull($app->data);
        $this->assertCount(2, get_object_vars($app->data));
        $this->assertObjectHasProperty($this->uploadField->code, $app->data);

        $uploadData = $app->data->{$this->uploadField->code};
        $this->assertEquals($body->data->{$this->uploadField->code}, $uploadData);
    }

    /**
     * @dataProvider saveApplicationDataProvider
     */
    public function testSaveApplicationWithMissingFile(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();
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
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->applicationMutationService->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::UNPROCESSABLE_ENTITY, $encryptedResponse->status);
        /** @var ValidationResultDTO $validationResultDTO */
        $validationResultDTO =
            $this->responseEncryptionService
                ->decryptCodable($encryptedResponse, ValidationResultDTO::class, $this->keyPair);
        $this->assertNotNull($validationResultDTO);
        $this->assertEquals('error', $validationResultDTO->validationResult['file'][0]->type);
        $this->assertEquals('File not found!', $validationResultDTO->validationResult['file'][0]->message);
    }

    public function testSaveApplicationWithValidationResult(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();
        Answer::factory()->for($applicationStage)->for($this->textField)->create();

        // Create draft application
        $body = new ApplicationSaveBody(
            (object)[
                $this->textField->code => "Test A",
            ],
            false
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->applicationMutationService->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $app = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, ApplicationDTO::class, $this->keyPair);

        $this->assertNotNull($app);
        $this->assertEquals($application->reference, $app->reference);
        $this->assertNotNull($app->data);
        $this->assertEquals("Test A", $app->data->text);

        $bankStatementFileId = $this->faker->uuid;
        $bankStatement = [
            ['id' => $bankStatementFileId, 'name' => $this->faker->word, 'mimeType' => 'application/pdf']
        ];

        // Update draft application for validation testing
        $body = new ApplicationSaveBody(
            (object)[
                $this->textField->code => "Test",
                $this->bankAccountHolderField->code => "Pieter",
                $this->bankAccountNumberField->code => "NL58ABNA9999142181",
                $this->bankStatementField->code => $bankStatement,
            ],
            false
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationSaveParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $fileManager = $this->app->get(ApplicationFileManager::class);
        $fileManager->writeFile($applicationStage, $this->bankStatementField, $bankStatementFileId, 'some-content');

        $encryptedResponse = $this->applicationMutationService->saveApplication($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $app = $this->responseEncryptionService->decrypt($encryptedResponse, $this->keyPair);

        $object = json_decode($app, true, 512, JSON_THROW_ON_ERROR);

        $this->assertNotNull($app);
        $this->assertEquals($application->reference, $object['reference']);
        $this->assertEquals([
            'bankAccountNumber' => [
                [
                    'type' => 'warning',
                    'message' => 'Naam rekeninghouder lijkt niet volledig te kloppen! Bedoelde u {suggestion}?',
                    'id' => 'validationSurePayWarning',
                    'params' => [
                        'suggestion' => [
                            'code' => 'bankAccountHolder',
                            'value' => 'Pietersma',
                        ]
                    ]
                ],
            ]
        ], $object['validationResult']);
    }
}
