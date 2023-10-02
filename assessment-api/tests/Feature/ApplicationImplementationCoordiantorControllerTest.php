<?php

declare(strict_types=1);

namespace Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Test\MocksEncryption;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @group application-coordinator
 * @group application-coordinator-controller
 */
class ApplicationImplementationCoordiantorControllerTest extends TestCase
{
    use DatabaseTransactions;
    use MocksEncryption;

    private SubsidyStage $subsidyStage1;
    private Application $application;

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function setUp(): void
    {
        parent::setUp();

        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->for($subsidy)->create(['status' => VersionStatus::Published]);
        $this->subsidyStage1 = SubsidyStage::factory()->for($subsidyVersion)->create([
            'stage' => 1,
            'subject_role' => SubjectRole::Applicant,
            'assessor_user_role' => RoleEnum::Assessor,
        ]);
        $subsidyStage2 = SubsidyStage::factory()->for($subsidyVersion)->create([
            'stage' => 2,
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => RoleEnum::Assessor,
        ]);
        $subsidyStage3 = SubsidyStage::factory()->for($subsidyVersion)->create([
            'stage' => 3,
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => RoleEnum::ImplementationCoordinator,
        ]);
        SubsidyStageTransition::factory()
            ->for($this->subsidyStage1, 'currentSubsidyStage')
            ->for($subsidyStage2, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Approved]);
        SubsidyStageTransition::factory()
            ->for($subsidyStage2, 'currentSubsidyStage')
            ->for($subsidyStage3, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Approved]);

        $identity = Identity::factory()->create();

        $application = Application::factory()->for($identity)->for($subsidyVersion)->create([
            'updated_at' => Carbon::today(),
            'created_at' => Carbon::today(),
            'final_review_deadline' => Carbon::tomorrow(),
        ]);

        ApplicationStage::factory()->for($application)->for($this->subsidyStage1)
            ->create(['is_current' => false, 'is_submitted' => true, 'submitted_at' => Carbon::now()]);
        ApplicationStage::factory()->for($application)->for($subsidyStage2)
            ->create(['is_current' => true, 'sequence_number' => 2]);

        $application->refresh();
        $this->application = $application;
    }


    public function testReleaseApplication(): void
    {
        $this->assertNotNull($this->application->currentApplicationStage);

        $user = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor);

        $this->application->currentApplicationStage->assessorUser()->associate($user);
        $this->application->currentApplicationStage->save();

        $implementationCoordinator = User::factory()->create();
        $implementationCoordinator->attachRole(RoleEnum::ImplementationCoordinator);

        $response = $this
            ->be($implementationCoordinator)
            ->json('DELETE', '/api/applications/' . $this->application->id . '/coordinator');
        $response->assertStatus(204);

        $this->application->refresh();
        $this->assertNotNull($this->application->currentApplicationStage);
        $this->assertNull($this->application->currentApplicationStage->assessor_user_id);
    }
}
