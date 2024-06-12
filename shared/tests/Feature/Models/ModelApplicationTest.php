<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Models;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role;

class ModelApplicationTest extends TestCase
{
    private Application $application;
    private ApplicationStage $applicationStage;
    private Identity $identity;
    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyVersion $falseSubsidyVersion;
    private Application $falseApplication;
    private Collection $subsidyStages;


    protected function setUp(): void
    {
        parent::setUp();

        $this->identity = Identity::factory()->create();
        $this->subsidy = Subsidy::factory()->create([
            'title' => 'Test Subsidy',
            'code' => 'TEST',
        ]);
        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy->id,
        ]);
        $this->subsidyStages = $this->setUpSubsidyWithStages($this->subsidyVersion);
        $this->application = Application::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'application_title' => 'Test Application',
            'created_at' => '2021-01-01',
            'final_review_deadline' => '2021-01-01',
            'updated_at' => '2021-01-01',
            'status' => ApplicationStatus::Pending,
        ]);

        $this->applicationStage = ApplicationStage::factory()->create([
            'application_id' => $this->application->id,
        ]);
        $this->falseSubsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => Subsidy::factory()->create()->id,
        ]);
        $this->falseApplication = Application::factory()->create();
    }

    public function testApplicationSubsidy(): void
    {
        $this->assertEquals($this->subsidy->id, $this->application->subsidy->id);
    }

    public function testScopeDates()
    {
        $query = Application::query()->createdAtFrom(Carbon::parse('2021-01-01'))->get();
        $this->assertTrue($query->contains('id', $this->application->id));
        $query = Application::query()->createdAtTo(Carbon::parse('2021-01-01'))->get();
        $this->assertTrue($query->contains('id', $this->application->id));
        $this->assertFalse($query->contains('id', $this->falseApplication->id));
        $query = Application::query()->updatedAtFrom(Carbon::parse('2021-01-01'))->get();
        $this->assertTrue($query->contains('id', $this->application->id));
        $query = Application::query()->updatedAtTo(Carbon::parse('2021-01-01'))->get();
        $this->assertTrue($query->contains('id', $this->application->id));
        $this->assertFalse($query->contains('id', $this->falseApplication->id));
    }

    public function testScopeApplicationTitle()
    {
        $query = Application::query()->title('Test Application')->get();

        $this->assertTrue($query->contains('id', $this->application->id));

        $this->assertFalse($query->contains('id', $this->falseApplication->id));
    }

    public function testScopeFinalReviewDeadlines()
    {
        $query = Application::query()->finalReviewDeadlineTo(Carbon::parse('2021-01-01'))->get();

        $this->assertTrue($query->contains('id', $this->application->id));

        $this->assertFalse($query->contains('id', $this->falseApplication->id));

        $query = Application::query()->finalReviewDeadlineFrom(Carbon::parse('2021-01-01'))->get();

        $this->assertTrue($query->contains('id', $this->application->id));
    }

    public function testScopeStatus()
    {
        $query = Application::query()->status([ApplicationStatus::Pending])->get();

        $this->assertTrue($query->contains('id', $this->application->id));

        $this->assertFalse($query->contains('id', $this->falseApplication->id));
    }

    public function testScopeSubsidyCode()
    {
        $query = Application::query()->subsidyCode(['TEST'])->get();

        $this->assertTrue($query->contains('subsidy_version_id', $this->subsidyVersion->id));

        $this->assertFalse($query->contains('subsidy_version_id', $this->falseSubsidyVersion->id));
    }

    public function testDraftAndValidSubsidyScope()
    {
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

        $applications = $this->identity->applications()
            ->where('status', ApplicationStatus::Draft)
            ->validSubsidy()
            ->get();

        $this->assertCount(1, $applications);
    }


    public function testDraftApplicationMultipleResults(): void
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
        $applications = $this->identity->applications()
            ->where('status', ApplicationStatus::Draft)
            ->validSubsidy()
            ->get();

        $this->assertCount(2, $applications);
    }

    public function testGetDraftApplicationMultipleApplications(): void
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
        $applications = $this->identity->applications()
            ->where('status', ApplicationStatus::Draft)
            ->validSubsidy()
            ->get();

        $this->assertCount(2, $applications);
    }

    public function testGetDraftApplicationMultipleApplicationsInDifferentSubsidies(): void
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
        $applications = $this->identity->applications()
            ->where('status', ApplicationStatus::Draft)
            ->validSubsidy()
            ->get();

        $this->assertCount(2, $applications);
    }


    public function testGetApplicationsOfRequestForChanges(): void
    {
        // Create a RequestForChanges application not expired
        $application1 = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::RequestForChanges,
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
                'expires_at' => CarbonImmutable::tomorrow(),
            ]);

        // Create a RequestForChanges application has expired
        $application2 = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => new DateTime('now'),
                'created_at' => new DateTime('now'),
                'final_review_deadline' => new DateTime('now'),
                'status' => ApplicationStatus::RequestForChanges,
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
                'expires_at' => CarbonImmutable::yesterday(),
            ]);

        // Application should be visible
        $applications = $this->identity->applications()
            ->where('status', ApplicationStatus::RequestForChanges)
            ->lastApplicationStageNotExpired()
            ->get();

        $this->assertCount(1, $applications);
    }


    public static function getApplicationSubsidyPeriodDataProvider()
    {
        return [
            'When draft outside subsidy period' => [
                'validTo' => CarbonImmutable::yesterday(),
                'applicationStatus' => ApplicationStatus::Draft,
                'resultCount' => 0
            ],
            'When draft inside subsidy period which has no end date' => [
                'validTo' => null,
                'applicationStatus' => ApplicationStatus::Draft,
                'resultCount' => 1
            ],
            'When RequestForChange outside subsidy period' => [
                'validTo' => CarbonImmutable::yesterday(),
                'applicationStatus' => ApplicationStatus::RequestForChanges,
                'resultCount' => 1
            ],
        ];
    }

    /**
     * @dataProvider getApplicationSubsidyPeriodDataProvider
     */
    public function testGetDraftOrRequestForChangesApplicationsWhenDraftOutsideSubsidyPeriod(
        ?DateTimeInterface $validTo,
        ApplicationStatus $applicationStatus,
        int $resultCount
    ): void {
        $this->subsidyVersion->subsidy->valid_from = CarbonImmutable::now()->subDays(100);
        $this->subsidyVersion->subsidy->valid_to = $validTo;
        $this->subsidyVersion->subsidy->save();

        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => CarbonImmutable::now()->subDays(5),
                'created_at' => CarbonImmutable::now()->subDays(10),
                'status' => $applicationStatus,
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStages->get(1))
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
                'expires_at' => CarbonImmutable::tomorrow(),
            ]);

        // Application should be visible
        $applications = $this->identity->applications()
            ->where(function (Builder $query) {
                $query->orWhere(function (Builder $query) {
                    $query->where('status', ApplicationStatus::RequestForChanges)
                        ->lastApplicationStageNotExpired();
                })->orWhere(function (Builder $query) {
                    $query->where('status', ApplicationStatus::Draft)
                        ->validSubsidy();
                });
            })
            ->get();

        $this->assertCount($resultCount, $applications);
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
