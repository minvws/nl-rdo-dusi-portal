<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Exception;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use MinVWS\Codable\Exceptions\ValueNotFoundException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationMetadata;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
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
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use Throwable;

/**
 * @group application
 * @group application-service
 */
class ApplicationServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use MocksEncryptionAndHashing;

    private SubsidyStage $subsidyStage;
    private Field $textField;
    private Field $numericField;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();
        $this->withoutFrontendEncryption();

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
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     * @group application-file-upload
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
                IdentityType::CitizenServiceNumber,
                base64_encode('123456789')
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
                IdentityType::CitizenServiceNumber,
                base64_encode('123456789')
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
                IdentityType::CitizenServiceNumber,
                base64_encode('123456789')
            ),
            new ApplicationMetadata($this->faker->uuid, $this->subsidyStage->id),
            json_encode($data)
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException($expectedException);
        $applicationService->processFormSubmit($formSubmit);
    }


    public function testProcessFormSubmitInvalidForm(): void
    {
        $formSubmit = new FormSubmit(
            new EncryptedIdentity(
                IdentityType::CitizenServiceNumber,
                base64_encode('123456789')
            ),
            new ApplicationMetadata($this->faker->uuid, $this->subsidyStage->id),
            json_encode([]) // Empty data for the form, which should be invalid
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException(Exception::class);
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
                IdentityType::CitizenServiceNumber,
                base64_encode('123456789')
            ),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->subsidyStage->id),
            json_encode($data)
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);

        $this->expectException(Exception::class);
        $applicationService->processFormSubmit($formSubmit);
    }
}
