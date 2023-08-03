<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Application;
use App\Models\ApplicationStage;
use App\Models\Disk;
use App\Models\Enums\ApplicationStageVersionStatus;
use App\Services\ApplicationService;
use App\Services\Exceptions\FileNotFoundException;
use App\Shared\Models\Application\ApplicationMetadata;
use App\Shared\Models\Application\FileUpload;
use App\Shared\Models\Application\FormSubmit;
use App\Shared\Models\Application\Identity;
use App\Shared\Models\Application\IdentityType;
use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\Enums\FieldType;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\Enums\VersionStatus;
use App\Shared\Models\Definition\SubsidyStage;
use App\Shared\Models\Definition\SubsidyVersion;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use MinVWS\Codable\Exceptions\ValueNotFoundException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
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
        ]);
        $this->numericField = Field::factory()->create([
            'type' => FieldType::TextNumeric,
            'code' => 'number']);

        $this->subsidyStage->fields()->attach($this->textField);
        $this->subsidyStage->fields()->attach($this->numericField);
    }

    public function testProcessFileUpload(): void
    {
        Storage::fake(Disk::APPLICATION_FILES);

        $this->subsidyStage->fields()->detach();
        $fileField = Field::factory()->create(['type' => FieldType::Upload, 'code' => 'file']);
        $fileField->subsidyStages()->attach($this->subsidyStage->id);


        $fileUpload = new FileUpload(
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
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

        $this->assertTrue(Storage::disk(Disk::APPLICATION_FILES)
            ->exists(sprintf("%s/%s", $fileUpload->applicationMetadata->applicationStageId, $fileField->code)));
        $applicationStage = ApplicationStage::query()->find($fileUpload->applicationMetadata->applicationStageId);
        $this->assertInstanceOf(ApplicationStage::class, $applicationStage);
        $this->assertEquals(ApplicationStageVersionStatus::Draft, $applicationStage->status);
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
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->subsidyStage->id),
            base64_encode(json_encode($data))
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $applicationStage = $applicationService->processFormSubmit($formSubmit);
        $this->assertNotNull($applicationStage);
        $this->assertEquals(ApplicationStageVersionStatus::Submitted, $applicationStage->status);

        $applicationStage = ApplicationStage::query()->find($formSubmit->applicationMetadata->applicationStageId);
        $this->assertInstanceOf(ApplicationStage::class, $applicationStage);
        $this->assertEquals(ApplicationStageVersionStatus::Submitted, $applicationStage->status);
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
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata($this->faker->uuid, $this->subsidyStage->id),
            base64_encode(json_encode($data))
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException($expectedException);
        $applicationService->processFormSubmit($formSubmit);
    }


    public function testProcessFormSubmitInvalidForm(): void
    {
        $formSubmit = new FormSubmit(
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata($this->faker->uuid, $this->subsidyStage->id),
            json_encode([]) // Empty data for the form, which should be invalid
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException(\Exception::class);
        $applicationService->processFormSubmit($formSubmit);
    }

    public function testProcessFormSubmitMissingFile(): void
    {
        Storage::fake(Disk::APPLICATION_FILES);

        $this->subsidyStage->fields()->detach();

        $fileField = Field::factory()->create(['type' => FieldType::Upload, 'code' => 'file']);
        $fileField->subsidyStages()->attach($this->subsidyStage->id);

        $data = [
            $this->textField->code => $this->faker->word,
            $this->numericField->code => $this->faker->randomDigit(),
            $fileField->code => $this->faker->uuid // we do add a file reference, but don't upload the file
        ];

        $formSubmit = new FormSubmit(
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->subsidyStage->id),
            base64_encode(json_encode($data))
        );

        $this->expectException(FileNotFoundException::class);
        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $applicationService->processFormSubmit($formSubmit);
    }
}
