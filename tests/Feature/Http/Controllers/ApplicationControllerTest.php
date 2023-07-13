<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\ProcessFileUpload;
use App\Jobs\ProcessFormSubmit;
use App\Models\DraftApplication;
use App\Models\PortalUser;
use App\Services\ApplicationService;
use App\Services\CacheService;
use App\Services\FormService;
use App\Services\StateService;
use App\Shared\Models\Application\IdentityType;
use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\FormUI;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\VersionStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Tests\WipesSubsidyDefinitions;

/**
 * @group application
 * @group application-controller
 */
class ApplicationControllerTest extends TestCase
{
    protected array $connectionsToTransact = [Connection::Form];

    use DatabaseTransactions;
    use WipesSubsidyDefinitions;
    use WithFaker;

    private Subsidy $subsidy;
    private Form $form;
    private Field $field;
    private FormUI $formUI;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create();
        $this->form = Form::factory()->create(['subsidy_id' => $this->subsidy->id, 'status' => VersionStatus::Published]);
        $this->field = Field::factory()->create(['form_id' => $this->form->id]);
        $this->ui = FormUI::factory()->create(['form_id' => $this->form->id, 'status' => VersionStatus::Published]);
        $this->app->get(CacheService::class)->cacheForm($this->form);
    }

    public function testCreateDraft(): void
    {
        $response = $this->postJson(route('api.application-create-draft', ['form' => $this->form->id]));
        $this->assertEquals(202, $response->status());
        $applicationId = $response->json('id');
        $this->assertIsString($applicationId);
        $application = $this->app->get(StateService::class)->getDraftApplication($applicationId);
        $this->assertNotNull($application);
        $this->assertEquals($applicationId, $application->id);
        $this->assertEquals($this->form->id, $application->formId);
    }

    public function testFileUpload(): void
    {
        Queue::fake();

        $user = new PortalUser(base64_encode(openssl_random_pseudo_bytes(32)), Uuid::uuid4(), null);

        $cachedForm = $this->app->get(FormService::class)->getForm($this->form->id);
        $applicationId = $this->app->get(ApplicationService::class)->createDraft($cachedForm);
        $fakeFile = UploadedFile::fake()->createWithContent('id.pdf', 'This should be an encrypted string');
        $data = ['fieldCode' => $this->field->code, 'file' => $fakeFile];
        $response = $this->actingAs($user)->postJson(route('api.application-upload-file', ['application' => $applicationId]), $data);
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
        $this->assertEquals($applicationId, $job->fileUpload->applicationMetadata->id);
        $this->assertEquals($this->form->id, $job->fileUpload->applicationMetadata->formId);
        $this->assertEquals($this->field->code, $job->fileUpload->fieldCode);
        $this->assertEquals('pdf', $job->fileUpload->extension);
        $this->assertEquals('application/pdf', $job->fileUpload->mimeType);
        $this->assertEquals(base64_encode($fakeFile->getContent()), $job->fileUpload->encryptedContents);
    }

    public function testFileUploadRequiresLogin(): void
    {
        Queue::fake();

        $cachedForm = $this->app->get(FormService::class)->getForm($this->form->id);
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

        $user = new PortalUser(base64_encode(openssl_random_pseudo_bytes(32)), Uuid::uuid4(), null);

        $cachedForm = $this->app->get(FormService::class)->getForm($this->form->id);
        $applicationId = $this->app->get(ApplicationService::class)->createDraft($cachedForm);
        $data['data'] = 'This should be an encrypted string';
        $response = $this->actingAs($user)->putJson(route('api.application-submit', ['application' => $applicationId]), $data);
        $this->assertEquals(202, $response->status());

        Queue::assertPushed(ProcessFormSubmit::class);
        $jobs = Queue::pushed(ProcessFormSubmit::class);
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertInstanceOf(ProcessFormSubmit::class, $job);
        $this->assertEquals(IdentityType::EncryptedCitizenServiceNumber, $job->formSubmit->identity->type);
        $this->assertEquals($applicationId, $job->formSubmit->applicationMetadata->id);
        $this->assertEquals($this->form->id, $job->formSubmit->applicationMetadata->formId);
        $this->assertEquals(base64_encode($data['data']), $job->formSubmit->encryptedData);
    }

    public function testSubmitRequiresLogin(): void
    {
        Queue::fake();

        $cachedForm = $this->app->get(FormService::class)->getForm($this->form->id);
        $applicationId = $this->app->get(ApplicationService::class)->createDraft($cachedForm);
        $data['data'] = 'This should be an encrypted string';
        $response = $this->putJson(route('api.application-submit', ['application' => $applicationId]), $data);
        $this->assertEquals(401, $response->status());

        Queue::assertNotPushed(ProcessFormSubmit::class);
    }
}
