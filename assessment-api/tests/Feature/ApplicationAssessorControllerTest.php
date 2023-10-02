<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

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
 * @group application-assessor
 * @group application-assessor-controller
 */
class ApplicationAssessorControllerTest extends TestCase
{
    use DatabaseTransactions;
    use MocksEncryption;

    private Subsidy $subsidy;
    private SubsidyStage $subsidyStage1;
    private Application $application;

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create(['status' => VersionStatus::Published]);
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

    public function testClaimApplication(): void
    {
        $this->assertNotNull($this->application->currentApplicationStage);
        $this->assertNull($this->application->currentApplicationStage->assessor_user_id);

        $user = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor);
        $response = $this
            ->be($user)
            ->json('PUT', '/api/applications/' . $this->application->id . '/assessor')
        ;
        $response->assertStatus(200);

        $this->application->refresh();
        $this->assertEquals($user->id, $this->application->currentApplicationStage?->assessor_user_id);
    }

    public function testClaimApplicationTwiceResultsInAnError(): void
    {
        $this->assertNotNull($this->application->currentApplicationStage);
        $this->assertNull($this->application->currentApplicationStage->assessor_user_id);

        $user = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor, $this->subsidy->id);
        $response = $this
            ->be($user)
            ->json('PUT', '/api/applications/' . $this->application->id . '/assessor');
        $response->assertStatus(200);

        $response = $this
            ->be($user)
            ->json('PUT', '/api/applications/' . $this->application->id . '/assessor');
        $response->assertStatus(403);
    }

    public function testClaimApplicationForAnotherSubsidyIsForbidden(): void
    {
        $this->assertNotNull($this->application->currentApplicationStage);
        $this->assertNull($this->application->currentApplicationStage->assessor_user_id);

        $user = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor, Subsidy::factory()->create()->id);
        $response = $this
            ->be($user)
            ->json('PUT', '/api/applications/' . $this->application->id . '/assessor');
        $response->assertForbidden();
    }

    public function testReleaseApplication(): void
    {
        $this->assertNotNull($this->application->currentApplicationStage);

        $user = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor, $this->subsidy->id);

        $this->application->currentApplicationStage->assessorUser()->associate($user);
        $this->application->currentApplicationStage->save();

        $response = $this
            ->be($user)
            ->json('DELETE', '/api/applications/' . $this->application->id . '/assessor');
        $response->assertStatus(204);

        $this->application->refresh();
        $this->assertNotNull($this->application->currentApplicationStage);
        $this->assertNull($this->application->currentApplicationStage->assessor_user_id);
    }

    public function testReleaseApplicationOfOtherUser(): void
    {
        $this->assertNotNull($this->application->currentApplicationStage);

        $user = User::factory()->create();
        $this->application->currentApplicationStage->assessorUser()->associate(User::factory()->create());
        $this->application->currentApplicationStage->save();

        $response =
            $this->be(User::factory()->create())
                ->json('DELETE', '/api/applications/' . $this->application->id . '/assessor');
        $response->assertStatus(403);
    }
}
