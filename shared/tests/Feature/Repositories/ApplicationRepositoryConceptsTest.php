<?php

declare(strict_types=1);

namespace Feature\Repositories;

use DateTime;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role;

/**
 * @group application-concepts
 */
class ApplicationRepositoryConceptsTest extends TestCase
{
    private ApplicationRepository $repository;

    private Identity $identity;
    private SubsidyVersion $subsidyVersion;
    private Collection $subsidyStages;

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
    }


    public function testGetMyConceptApplicationEmptyResult(): void
    {
        // Test without applications
        $applications = $this->repository->getMyConceptApplications($this->identity, $this->subsidyVersion->subsidy);
        $this->assertCount(0, $applications);
    }


    public function testGetMyConceptApplicationSingleResult(): void
    {
        // Test without applications
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
        $applications = $this->repository->getMyConceptApplications($this->identity, $this->subsidyVersion->subsidy);
        $this->assertCount(1, $applications);
    }

    public function testGetMyConceptApplicationMultipleResults(): void
    {
        // Create a test application
        $application1 = Application::factory()
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
            ->for($application1)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Create a test application
        $application2 = Application::factory()
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
            ->for($application2)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Application should be visible
        $applications = $this->repository->getMyConceptApplications($this->identity, $this->subsidyVersion->subsidy);
        $this->assertCount(2, $applications);
    }

    public function testGetMyConceptApplicationMultipleApplications(): void
    {
        // Create a test application
        $application1 = Application::factory()
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
            ->for($application1)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Create a test application
        $application2 = Application::factory()
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
            ->for($application2)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => new DateTime('now'),
            ]);

        // Application should be visible
        $applications = $this->repository->getMyConceptApplications($this->identity, $this->subsidyVersion->subsidy);
        $this->assertCount(1, $applications);
    }
    public function testGetMyConceptApplicationMultipleApplicationsInDifferentSubsidies(): void
    {
        // Create a test application
        $application1 = Application::factory()
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
            ->for($application1)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);


        // Create a test application in a different subsidy
        $subsidy2 = Subsidy::factory()->create([
            'title' => 'some_subsidy_title',
            'code' => 'SST',
        ]);
        $subsidyVersion2 = SubsidyVersion::factory()->for($subsidy2)->create();
        $subsidyStages2 = $this->setUpSubsidyWithStages($subsidyVersion2);

        $application2 = Application::factory()
            ->for($this->identity)
            ->for($subsidyVersion2)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::Draft,
            ]);

        // Create application stages
        ApplicationStage::factory()
            ->for($application2)
            ->for($subsidyStages2->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);

        // Application should be visible
        $applications = $this->repository->getMyConceptApplications($this->identity, $this->subsidyVersion->subsidy);
        $this->assertCount(1, $applications);
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
}
