<?php

declare(strict_types=1);

namespace Feature\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Test\MocksEncryption;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @group application-show
 */
class ApplicationControllerShowTest extends TestCase
{
    use DatabaseTransactions;
    use MocksEncryption;

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

        $this->setupMocksEncryption();

        $this->flowService = $this->app->get(ApplicationFlowService::class);
        $this->encryptionService = $this->app->get(ApplicationStageEncryptionService::class);

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create(['status' => VersionStatus::Published]);
        $this->subsidyStage1 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 1,
            'subject_role' => SubjectRole::Applicant,
            'assessor_user_role' => RoleEnum::Assessor,
        ]);
        $this->subsidyStage2 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 2,
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => RoleEnum::Assessor,
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

    /**
     * @dataProvider showUnassignedApplicationDataProvider
     */
    public function testShowUnassignedApplication(RoleEnum $roleEnum, bool $allowed): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();

        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage1 = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create([
            'sequence_number' => 1,
            'encrypted_key' => $encryptedKey
        ]);

        $user = User::factory()->create();
        $user->attachRole($roleEnum);

        $this->flowService->submitApplicationStage($applicationStage1);

        $response = $this
            ->be($user)
            ->getJson(sprintf('/api/applications/%s', $application->id));

        if ($allowed) {
            $response->assertOk();
        } else {
            $response->assertForbidden();
        }
    }

    /**
     * @dataProvider showAssignedApplicationDataProvider
     */
    public function testShowAssignedApplication(RoleEnum $roleEnum, bool $allowed): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();

        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage1 = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create([
            'sequence_number' => 1,
            'encrypted_key' => $encryptedKey
        ]);

        $user = User::factory()->create();
        $user->attachRole($roleEnum);

        $currentApplicationStage = $this->flowService->submitApplicationStage($applicationStage1);
        $currentApplicationStage->assessor_user_id = $user->id;
        $currentApplicationStage->save();

        $response = $this
            ->be($user)
            ->getJson(sprintf('/api/applications/%s', $application->id));

        if ($allowed) {
            $response->assertOk();
        } else {
            $response->assertForbidden();
        }
    }

    public static function showUnassignedApplicationDataProvider()
    {
        return [
            'Assessor' => [RoleEnum::Assessor, false],
            'InternalAuditor' => [RoleEnum::InternalAuditor, false],
            'ImplementationCoordinator' => [RoleEnum::ImplementationCoordinator, true]
        ];
    }

    public static function showAssignedApplicationDataProvider()
    {
        return [
            'Assessor' => [RoleEnum::Assessor, true],
            'InternalAuditor' => [RoleEnum::InternalAuditor, true],
            'ImplementationCoordinator' => [RoleEnum::ImplementationCoordinator, true]
        ];
    }

    public function testShowAssessmentForOtherUserIsForbidden(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();

        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage1 = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create([
            'sequence_number' => 1,
            'encrypted_key' => $encryptedKey
        ]);

        $user = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor);

        $currentApplicationStage = $this->flowService->submitApplicationStage($applicationStage1);
        $currentApplicationStage->assessor_user_id = $user->id;
        $currentApplicationStage->save();

        $anotherUser = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor);

        $response = $this
            ->be($anotherUser)
            ->getJson(sprintf('/api/applications/%s', $application->id));

        $response->assertForbidden();
    }

    public function testShowAssessmentForUserWhichHasAssessedBeforeIsAllowed(): void
    {
        $subsidyStage4 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 3,
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => RoleEnum::Assessor,
        ]);

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage3, 'currentSubsidyStage')
            ->for($subsidyStage4, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Approved]);

        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();

        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage1 = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create([
            'sequence_number' => 1,
            'encrypted_key' => $encryptedKey
        ]);

        $user = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor);

        $nextApplicationStage = $this->flowService->submitApplicationStage($applicationStage1);
        $nextApplicationStage->assessor_user_id = $user->id;
        $nextApplicationStage->save();

        $anotherUser = User::factory()->create();
        $user->attachRole(RoleEnum::Assessor);

        $currentApplicationStage = $this->flowService->submitApplicationStage($nextApplicationStage);
        $currentApplicationStage->assessor_user_id = $anotherUser->id;
        $currentApplicationStage->save();

        $response = $this
            ->be($anotherUser)
            ->getJson(sprintf('/api/applications/%s', $application->id));

        $response->assertOk();
    }
}
