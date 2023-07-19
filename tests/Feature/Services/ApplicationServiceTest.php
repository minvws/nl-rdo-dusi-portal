<?php
declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Application;
use App\Models\Disk;
use App\Models\Enums\ApplicationStatus;
use App\Services\ApplicationService;
use App\Services\Exceptions\FileNotFoundException;
use App\Shared\Models\Application\ApplicationMetadata;
use App\Shared\Models\Application\FileUpload;
use App\Shared\Models\Application\FormSubmit;
use App\Shared\Models\Application\Identity;
use App\Shared\Models\Application\IdentityType;
use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\FieldType;
use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\VersionStatus;
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

    private Form $form;
    private Field $textField;
    private Field $numericField;

    protected function setUp(): void
    {
        parent::setUp();
        $subsidy = Subsidy::factory()->create();
        $this->form = Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Published]);
        $this->textField = Field::factory()->create(['form_id' => $this->form->id, 'type' => FieldType::Text, 'code' => 'text']);
        $this->numericField = Field::factory()->create(['form_id' => $this->form->id, 'type' => FieldType::TextNumeric, 'code' => 'number']);
    }

    public function testProcessFileUpload(): void
    {
        Storage::fake(Disk::ApplicationFiles);

        $fileField = Field::factory()->create(['form_id' => $this->form->id, 'type' => FieldType::Upload, 'code' => 'file']);

        $fileUpload = new FileUpload(
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->form->id),
            $fileField->code,
            Uuid::uuid4()->toString(),
            'application/pdf',
            'pdf',
            base64_encode(openssl_random_pseudo_bytes(1024))
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $applicationService->processFileUpload($fileUpload);

        $this->assertTrue(Storage::disk(Disk::ApplicationFiles)->exists(sprintf("%s/%s", $fileUpload->applicationMetadata->id, $fileField->code)));
        $application = Application::query()->find($fileUpload->applicationMetadata->id);
        $this->assertInstanceOf(Application::class, $application);
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
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->form->id),
            json_encode($data)
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $application = $applicationService->processFormSubmit($formSubmit);
        $this->assertNotNull($application);
        $this->assertEquals(ApplicationStatus::Submitted, $application->status);

        $application = Application::query()->find($formSubmit->applicationMetadata->id);
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
            $this->textField->code => $text,
            $this->numericField->code => $numeric,
        ];

        $formSubmit = new FormSubmit(
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->form->id),
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
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->form->id),
            json_encode([])
        );

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException(\Exception::class);
        $applicationService->processFormSubmit($formSubmit);
    }

    public function testProcessFormSubmitMissingFile(): void
    {
        Storage::fake(Disk::ApplicationFiles);

        $fileField = Field::factory()->create(['form_id' => $this->form->id, 'type' => FieldType::Upload, 'code' => 'file']);

        $data = [
            $this->textField->code => $this->faker->word,
            $this->numericField->code => $this->faker->randomDigit(),
            $fileField->code => $this->faker->uuid // we do add a file reference, but don't upload the file
        ];

        $formSubmit = new FormSubmit(
            new Identity(IdentityType::EncryptedCitizenServiceNumber, base64_encode(openssl_random_pseudo_bytes(32))),
            new ApplicationMetadata(Uuid::uuid4()->toString(), $this->form->id),
            json_encode($data)
        );

        $this->expectException(FileNotFoundException::class);
        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $application = $applicationService->processFormSubmit($formSubmit);
    }
}
