<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Assessment\API\Http\Middleware\Authenticate;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

/**
 * @group assessment-submit
 */
class ApplicationControllerSubmitTest extends TestCase
{
    use DatabaseTransactions;

    private ApplicationFlowService $flowService;
    private ApplicationStageEncryptionService $encryptionService;


    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStage $subsidyStage2;
    private SubsidyStage $subsidyStage3;
    private Field $statusField;

    protected function setUp(): void
    {
        parent::setUp();

        //ToDo: Disabled authentication temporarily
        $this->withoutMiddleware(Authenticate::class);

        $this->flowService = $this->app->get(ApplicationFlowService::class);
        $this->encryptionService = $this->app->get(ApplicationStageEncryptionService::class);

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create(['status' => VersionStatus::Published]);
        $this->subsidyStage1 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 1,
            'subject_role' => SubjectRole::Applicant
        ]);
        $this->subsidyStage2 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 2,
            'subject_role' => SubjectRole::Assessor
        ]);
        $this->statusField = Field::factory()->for($this->subsidyStage2)->create([
            'code' => 'firstAssessment',
            'type' => FieldType::Select,
            'params' => [
                'options' => [
                    "Onbeoordeeld",
                    "Aanvulling nodig",
                    "Afgekeurd",
                    "Goedgekeurd"
                ]
            ]
        ]);

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage1, 'currentSubsidyStage')
            ->for($this->subsidyStage2, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Approved]);

        $this->subsidyStage3 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
                'stage' => 3,
                'subject_role' => SubjectRole::Assessor
            ]);
        SubsidyStageTransition::factory()
            ->for($this->subsidyStage2, 'currentSubsidyStage')
            ->for($this->subsidyStage3, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Approved]);

        $this->identity = Identity::factory()->create();
    }

    public function testSubmitAssessment(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create([
            'updated_at' => Carbon::today(),
            'created_at' => Carbon::today(),
            'final_review_deadline' => Carbon::today(),
        ]);

        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage1 = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create([
            'sequence_number' => 1,
            'encrypted_key' => $encryptedKey
        ]);

        $this->flowService->submitApplicationStage($applicationStage1);

        $body = new ApplicationSaveBody(
            (object)[
                $this->statusField->code => 'Goedgekeurd',
            ],
            true
        );

        $json = (new JSONEncoder())->encode($body);

        $response = $this->putJson(sprintf('/api/applications/%s', $application->id), json_decode($json, true));

        $response->assertOk();

        $application->refresh();
        $this->assertEquals(3, $application->currentApplicationStage->sequence_number);
        $this->assertTrue($application->currentApplicationStage->is_current);
    }

    public function testShowAssessment(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();

        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage1 = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create([
            'sequence_number' => 1,
            'encrypted_key' => $encryptedKey
        ]);

        $this->flowService->submitApplicationStage($applicationStage1);

        $response = $this->getJson(sprintf('/api/applications/%s', $application->id));

        $response->assertOk();
    }
}
