<?php

declare(strict_types=1);

namespace Feature\Repositories;

use DateTime;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\PaginationOptions;
use MinVWS\DUSi\Shared\Application\DTO\SortOptions;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStageDecision;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;

class ApplicationRepositoryWithStagesTest extends TestCase
{
    private ApplicationRepository $repository;

    private Identity $identity;
    private SubsidyVersion $subsidyVersion;
    private Collection $subsidyStages;
    private User $implementationCoordinatorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->identity = Identity::factory()->create();

        $subsidy = Subsidy::factory()->create([
            'title' => 'some_subsidy_title',
            'code' => 'SST',
        ]);
        $this->subsidyVersion = SubsidyVersion::factory()->for($subsidy)->create();
        $this->subsidyStages = $this->setUpSubsidyWithStages($this->subsidyVersion);

        $this->repository = new ApplicationRepository();

        $this->implementationCoordinatorUser = User::factory()
            ->withRole(Role::ImplementationCoordinator)
            ->create();
    }


    public function testImplementationCoordinatorsCanNotViewDraftApplicationsInStage1(): void
    {
        // Test without applications
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);

        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::Draft,
            ]);

        // Create application stages
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Application should be visible
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);
    }

    public function testImplementationCoordinatorsCanViewApplicationInStage2(): void
    {
        // Test without applications
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);

        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::Pending,
            ]);

        // Create application stages
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(2))
            ->create([
                'sequence_number' => 2,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Application should be visible
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(1, $applications);
    }

    public function testImplementationCoordinatorsCanViewApplicationInStage3(): void
    {
        // Test without applications
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);

        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::Pending,
            ]);

        // Create application stages
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(2))
            ->create([
                'sequence_number' => 2,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 3,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Application should be visible
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(1, $applications);
    }

    public function testImplementationCoordinatorsCanViewApplicationInStage4(): void
    {
        // Test without applications
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);

        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::Pending,
            ]);

        // Create application stages
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(2))
            ->create([
                'sequence_number' => 2,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 3,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 4,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Application should be visible
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(1, $applications);
    }

    public function testImplementationCoordinatorsCanViewApplicationInStage5(): void
    {
        // Test without applications
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);

        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::Pending,
            ]);

        // Create application stages
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(2))
            ->create([
                'sequence_number' => 2,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);


        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 3,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);


        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 4,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 5,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Application should be visible
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(1, $applications);
    }

    public function testImplementationCoordinatorsCanNotViewApprovedApplication(): void
    {
        // Test without applications
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);

        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::Approved,
            ]);

        // Create application stages
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(2))
            ->create([
                'sequence_number' => 2,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);


        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 3,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);


        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 4,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(3))
            ->create([
                'sequence_number' => 5,
                'is_current' => false,
                'is_submitted' => true,
                'assessor_user_id' => $this->implementationCoordinatorUser->id,
                'assessor_decision' => ApplicationStageDecision::Approved,
                'submitted_at' => new DateTime('now'),
            ]);

        // Application should be visible
        $applications = $this->getApplications($this->implementationCoordinatorUser);
        $this->assertCount(0, $applications);
    }


    protected function setUpSubsidyWithStages(SubsidyVersion $subsidyVersion): Collection
    {
        SubsidyStage::create([
            'subsidy_version_id' => $subsidyVersion->id,
            'title' => 'Aanvraag',
            'subject_role' => SubjectRole::Applicant->value,
            'stage' => 1,
        ]);

        SubsidyStage::create([
            'subsidy_version_id' => $subsidyVersion->id,
            'title' => 'Eerste beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 2,
            'internal_note_field_code' => 'firstAssessmentInternalNote'
        ]);

        SubsidyStage::create([
            'subsidy_version_id' => $subsidyVersion->id,
            'title' => 'Tweede beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 3,
            'internal_note_field_code' => 'secondAssessmentInternalNote'
        ]);

        SubsidyStage::create([
            'subsidy_version_id' => $subsidyVersion->id,
            'title' => 'Interne controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::InternalAuditor->value,
            'stage' => 4,
            'internal_note_field_code' => 'internalAssessmentInternalNote'
        ]);

        SubsidyStage::create([
            'subsidy_version_id' => $subsidyVersion->id,
            'title' => 'Uitvoeringscoördinator controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::ImplementationCoordinator->value,
            'stage' => 5,
            'internal_note_field_code' => 'coordinatorImplementationInternalNote'
        ]);

        return $subsidyVersion
            ->subsidyStages()
            ->orderBy('stage')
            ->get()
            ->mapWithKeys(
                fn (SubsidyStage $stage) => [$stage->stage => $stage]
            );
    }

    protected function getApplications(User $user): array
    {
        $applicationsFilter = new ApplicationsFilter();
        $applicationsPaginationOptions = new PaginationOptions(1, 15);
        $sortOptions = new SortOptions();

        return $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $applicationsFilter,
            paginationOptions: $applicationsPaginationOptions,
            sortOptions: $sortOptions,
        )->items();
    }
}
