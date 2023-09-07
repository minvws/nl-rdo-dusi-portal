<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use MinVWS\Codable\Exceptions\ValueNotFoundException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use MinVWS\DUSi\Application\Backend\Interfaces\KeyReader;
use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Application\Backend\Services\EncryptionService;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\FormSubmitInvalidBodyReceivedException;
use MinVWS\DUSi\Application\Backend\Services\Hsm\HsmService;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * @group application
 * @group application-service
 */
class ApplicationServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private SubsidyStage $subsidyStage;
    private Field $textField;
    private Field $numericField;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()
            ->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Published]);
        $this->subsidyStage = SubsidyStage::factory()->create(
            [
                'subsidy_version_id' => $subsidyVersion->id,
            ]
        );
        $this->textField = Field::factory()->create([
            'type' => FieldType::Text,
            'code' => 'text',
            'subsidy_stage_id' => $this->subsidyStage->id,
        ]);
        $this->numericField = Field::factory()->create([
            'type' => FieldType::TextNumeric,
            'code' => 'number',
            'subsidy_stage_id' => $this->subsidyStage->id,
        ]);

        $keyReader = $this->getMockBuilder(KeyReader::class)
            ->getMock();

        $hsmService = $this->getMockBuilder(HsmService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $encryptionServiceMock = $this->getMockBuilder(EncryptionService::class)
            ->setConstructorArgs([$keyReader, $hsmService])
            ->getMock();

        // Configure the decryptData method to return the same value as the input parameter
        $encryptionServiceMock->expects($this->any())
            ->method('decryptBase64EncodedData')
            ->willReturnCallback(function ($input) {
                return $input;
            });

        $encryptionServiceMock->expects($this->any())
            ->method('decryptData')
            ->willReturnCallback(function ($input) {
                return $input;
            });

        $encryptionServiceMock->expects($this->any())
            ->method('decryptIdentity')
            ->willReturnCallback(function ($input) {
                assert($input instanceof EncryptedIdentity);
                return new Identity($input->type, $input->encryptedIdentifier);
            });

        $encryptionServiceMock->expects($this->any())
            ->method('encryptData')
            ->willReturnCallback(function ($input) {
                return $input;
            });

        $this->app->instance(EncryptionService::class, $encryptionServiceMock);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    public function testProcessFileUpload(): void
    {
        Storage::fake(Disk::APPLICATION_FILES);

        $this->subsidyStage->fields()->delete();
        $fileField = Field::factory()
            ->for($this->subsidyStage)
            ->create([
                'type' => FieldType::Upload,
                'code' => 'file',
            ]);

        $fileUpload = new FileUpload(
            new EncryptedIdentity(
                IdentityType::EncryptedCitizenServiceNumber,
                '123456789'
            ),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->subsidyStage->id),
            $fileField->code,
            Uuid::uuid4()->toString(),
            'application/pdf',
            'pdf',
            base64_encode(openssl_random_pseudo_bytes(1024))
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $applicationService->processFileUpload($fileUpload);

        $application = Application::query()->find($fileUpload->applicationMetadata->applicationId);
        $this->assertInstanceOf(Application::class, $application);

        $this->assertTrue(Storage::disk(Disk::APPLICATION_FILES)
            ->exists(sprintf("%s/%s", $application->currentApplicationStage->id, $fileField->code)));
        $this->assertEquals(ApplicationStatus::Draft, $application->status);
    }

    /**
     * @throws Throwable
     */
    public function testProcessFormSubmit(): void
    {
        $data = [
            $this->textField->code => $this->faker->word,
            $this->numericField->code => $this->faker->randomDigit(),
        ];

        $formSubmit = new FormSubmit(
            new EncryptedIdentity(
                IdentityType::EncryptedCitizenServiceNumber,
                '123456789'
            ),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->subsidyStage->id),
            json_encode($data)
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $applicationService->processFormSubmit($formSubmit);

        $application = Application::query()->find($formSubmit->applicationMetadata->applicationId);
        $this->assertInstanceOf(Application::class, $application);
        $this->assertEquals(ApplicationStatus::Submitted, $application->status);
    }


    public static function invalidFormSubmitProvider(): Generator
    {
        yield 'text-should-be-string' => [123, 123, ValueTypeMismatchException::class];
        yield 'text-should-not-be-null' => [null, 123, ValueNotFoundException::class];
        yield 'numeric-should-be-int' => ['text', 'text', ValueTypeMismatchException::class];
        yield 'numeric-should-not-be-null' => ['text', null, ValueNotFoundException::class];
    }

    /**
     * @dataProvider invalidFormSubmitProvider
     */
    public function testProcessFormSubmitInvalidFieldData(mixed $text, mixed $numeric, string $expectedException): void
    {
        $data = [
            $this->textField->code => $text, // Invalid data for a text field
            $this->numericField->code => $numeric, // Invalid data for a numeric field
        ];

        $formSubmit = new FormSubmit(
            new EncryptedIdentity(
                IdentityType::EncryptedCitizenServiceNumber,
                '123456789'
            ),
            new ApplicationMetadata($this->faker->uuid, $this->subsidyStage->id),
            json_encode($data)
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);

        try {
            $applicationService->processFormSubmit($formSubmit);
        } catch (FormSubmitInvalidBodyReceivedException $exception) {
            $this->assertEquals($expectedException, $exception->getPrevious()::class);
            return;
        }

        $this->fail("Expected exception $expectedException was not thrown");
    }


    public function testProcessFormSubmitInvalidForm(): void
    {
        $formSubmit = new FormSubmit(
            new EncryptedIdentity(
                IdentityType::EncryptedCitizenServiceNumber,
                '123456789'
            ),
            new ApplicationMetadata($this->faker->uuid, $this->subsidyStage->id),
            json_encode([]) // Empty data for the form, which should be invalid
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException(FormSubmitInvalidBodyReceivedException::class);
        $applicationService->processFormSubmit($formSubmit);
    }

    public function testProcessFormSubmitMissingFile(): void
    {
        Storage::fake(Disk::APPLICATION_FILES);

        $this->subsidyStage->fields()->delete();

        $fileField = Field::factory()->create([
            'type' => FieldType::Upload,
            'code' => 'file',
            'subsidy_stage_id' => $this->subsidyStage->id
        ]);

        $data = [
            $this->textField->code => $this->faker->word,
            $this->numericField->code => $this->faker->randomDigit(),
            $fileField->code => $this->faker->uuid // we do add a file reference, but don't upload the file
        ];

        $formSubmit = new FormSubmit(
            new EncryptedIdentity(
                IdentityType::EncryptedCitizenServiceNumber,
                '123456789'
            ),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->subsidyStage->id),
            json_encode($data)
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $applicationStage = $applicationService->processFormSubmit($formSubmit);

        $applicationStageVersion = (new ApplicationRepository())->getLatestApplicationStageVersion($applicationStage);
        // The application stage version should be invalid, because the file is missing
        $this->assertNotNull($applicationStageVersion);
        $this->assertEquals(ApplicationStageVersionStatus::Invalid, $applicationStageVersion->status);
    }

    /**
     * Test field validation fails when a required field is missing
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function testValidationFailsRequiredFieldSubmittedApplicationInvalid(): void
    {
        $this->subsidyStage->fields()->delete();

        $requiredTextField = Field::factory()->create([
            'type' => FieldType::Text,
            'code' => 'required_text_field',
            'subsidy_stage_id' => $this->subsidyStage->id,
            'is_required' => true,
        ]);

        $data = [
            $this->textField->code => $this->faker->word,
            $this->numericField->code => $this->faker->randomDigit(),
            $requiredTextField->code => null,
        ];

        $formSubmit = new FormSubmit(
            new Identity(
                IdentityType::EncryptedCitizenServiceNumber,
                base64_encode(openssl_random_pseudo_bytes(32))
            ),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->subsidyStage->id),
            json_encode($data)
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $applicationStage = $applicationService->processFormSubmit($formSubmit);

        $applicationStageVersion = (new ApplicationRepository())->getLatestApplicationStageVersion($applicationStage);
        // The application stage version should be invalid, because the file is missing
        $this->assertNotNull($applicationStageVersion);
        $this->assertEquals(ApplicationStageVersionStatus::Invalid, $applicationStageVersion->status);
    }
}
