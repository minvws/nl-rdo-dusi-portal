<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Jobs\ProcessFileUpload;
use App\Jobs\ProcessFormSubmit;
use App\Models\PortalUser;
use App\Services\ApplicationService;
use App\Services\CacheService;
use App\Services\StateService;
use App\Services\SubsidyStageService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use MinVWS\DUSi\Shared\Application\Shared\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageUI;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

/**
 * @group application
 * @group application-controller
 */
class ApplicationControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::FORM];

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
        $this->subsidyVersion = $this->subsidy
            ->subsidyVersions()
            ->create(['status' => VersionStatus::Published, 'version' => 1, 'subsidy_page_url' => 'https://dus-i.nl']);
        $this->subsidyStage = SubsidyStage::factory()->create(
            ['subsidy_version_id' => $this->subsidyVersion->id]
        );
        $this->field = Field::factory()->create();
        $this->subsidyStage->fields()->attach($this->field);
        $this->ui = SubsidyStageUI::factory()->create([
            'subsidy_stage_id' => $this->subsidyStage->id,
            'status' => VersionStatus::Published
        ]);
        $this->app->get(CacheService::class)->cacheSubsidyStage($this->subsidyStage);
    }

    public function testCreateDraft(): void
    {
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

    public function testFileUpload(): void
    {
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
        $this->assertEquals(IdentityType::EncryptedCitizenServiceNumber, $job->fileUpload->identity->type);
        $this->assertEquals($applicationId, $job->fileUpload->applicationMetadata->applicationStageId);
        $this->assertEquals($this->subsidyStage->id, $job->fileUpload->applicationMetadata->subsidyStageId);
        $this->assertEquals($this->field->code, $job->fileUpload->fieldCode);
        $this->assertEquals('pdf', $job->fileUpload->extension);
        $this->assertEquals('application/pdf', $job->fileUpload->mimeType);
        $this->assertEquals(base64_encode($fakeFile->getContent()), $job->fileUpload->encryptedContents);
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
        $this->assertEquals(IdentityType::EncryptedCitizenServiceNumber, $job->formSubmit->identity->type);
        $this->assertEquals($applicationId, $job->formSubmit->applicationMetadata->applicationStageId);
        $this->assertEquals($this->subsidyStage->id, $job->formSubmit->applicationMetadata->subsidyStageId);
        $this->assertEquals(base64_encode($data['data']), $job->formSubmit->encryptedData);
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
