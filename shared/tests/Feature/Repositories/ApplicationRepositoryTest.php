<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Repositories;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\DTO\PaginationOptions;
use MinVWS\DUSi\Shared\Application\DTO\SortColumn;
use MinVWS\DUSi\Shared\Application\DTO\SortOptions;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;

class ApplicationRepositoryTest extends TestCase
{
    private ApplicationRepository $repository;

    private Identity $identity;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage;

    protected function setUp(): void
    {
        parent::setUp();
        $subsidy = Subsidy::factory()->create([
            'title' => 'some_subsidy_title',
            'code' => 'SST',
        ]);
        $this->identity = Identity::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()->for($subsidy)->create();
        $this->subsidyStage = SubsidyStage::factory()->for($this->subsidyVersion)->create(['stage' => 1]);
        $this->repository = new ApplicationRepository();

        User::factory()->make();
    }

    /**
     * @throws \Exception
     */
    public function testGetApplicationWith()
    {
        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create(
                [
                'application_title' => 'some_application_title',
                'updated_at' => new \DateTime('now'),
                'created_at' => new \DateTime('now'),
                'final_review_deadline' => new \DateTime('now'),
                'status' => ApplicationStatus::Pending,
                ]
            );

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStage)
            ->create()->id;

        $user = User::factory()->create();
        $user->attachRole(Role::Assessor);

        $filter = [
            'application_title' => 'some_application_title',
            'date_from' => (new \DateTime())->createFromFormat('U', (string)strtotime('yesterday')),
            'date_to' => (new \DateTime())->createFromFormat('U', (string)strtotime('tomorrow')),
            'date_last_modified_from' => (new \DateTime())->createFromFormat('U', (string)strtotime('yesterday')),
            'date_last_modified_to' => (new \DateTime())->createFromFormat('U', (string)strtotime('tomorrow')),
            'date_final_review_deadline_from' => (new \DateTime())
                ->createFromFormat('U', (string)strtotime('yesterday')),
            'date_final_review_deadline_to' => (new \DateTime())
                ->createFromFormat('U', (string)strtotime('tomorrow')),
            'status' => [ApplicationStatus::Pending],
            'subsidy' => ['SST'],
        ];
        $appFilter = ApplicationsFilter::fromArray($filter);

        $applications = $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $appFilter,
            paginationOptions: new PaginationOptions(1, 15),
            sortOptions: new SortOptions()
        )->items();

        $foundApplication = $applications[0];

        // Test valid application
        $this->assertInstanceOf(Application::class, $foundApplication);
        $this->assertEquals($foundApplication->subsidyVersion->id, $this->subsidyVersion->id);

        // Test invalid application
        $filter = [
            'application_title' => 'invalid_application_title',
        ];

        $appFilter = ApplicationsFilter::fromArray($filter);

        $applications = $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $appFilter,
            paginationOptions: new PaginationOptions(1, 15),
            sortOptions: new SortOptions()
        )->items();

        $this->assertEmpty($applications);
    }

    public function testGetApplicationPaginated()
    {
        Application::factory()
            ->count(50)
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->withApplicantStage($this->subsidyStage)
            ->create([
                'status' => ApplicationStatus::Pending,
            ]);

        $user = User::factory()->create();
        $user->attachRole(Role::Assessor);

        $appFilter = new ApplicationsFilter();
        $paginationOptions = new PaginationOptions(1, 15);
        $paginatedApplications = $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $appFilter,
            paginationOptions: $paginationOptions,
            sortOptions: new SortOptions(),
        );

        $this->assertSame(50, $paginatedApplications->total());
        $this->assertSame(15, $paginatedApplications->perPage());
        $this->assertSame(1, $paginatedApplications->currentPage());

        $paginationOptions = new PaginationOptions(2, 15);
        $paginatedApplications = $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $appFilter,
            paginationOptions: $paginationOptions,
            sortOptions: new SortOptions(),
        );

        $this->assertSame(50, $paginatedApplications->total());
        $this->assertSame(15, $paginatedApplications->perPage());
        $this->assertSame(2, $paginatedApplications->currentPage());
    }

    public function testGetAllApplications()
    {
        Application::factory()
            ->count(50)
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->withApplicantStage($this->subsidyStage)
            ->create([
                'status' => ApplicationStatus::Pending,
            ]);

        $user = User::factory()->create();
        $user->attachRole(Role::Assessor);

        $appFilter = new ApplicationsFilter();

        $applicationsPaginated = $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $appFilter,
            paginationOptions: new PaginationOptions(1, 15),
            sortOptions: new SortOptions(),
        );
        $this->assertSame(50, $applicationsPaginated->total());
        $this->assertSame(1, $applicationsPaginated->currentPage());
        $this->assertCount(15, $applicationsPaginated->items());
    }

    public function testGetApplication()
    {
        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create();

        // Test valid application
        $foundApplication = $this->repository->getApplication($application->id);
        $this->assertInstanceOf(Application::class, $foundApplication);

        // Test invalid application
        $this->expectException(QueryException::class);
        $this->repository->getApplication('invalid_application_id');
    }

    public function testGetApplicationStage()
    {
        // Create a test application stage
        $applicationStage = ApplicationStage::factory()->create();

        // Test valid application stage
        $foundApplicationStage = $this->repository->getApplicationStage($applicationStage->id);
        $this->assertInstanceOf(ApplicationStage::class, $foundApplicationStage);

        // Test invalid application stage
        $this->expectException(QueryException::class);
        $this->repository->getApplicationStage('invalid_application_stage_id');
    }

    public function testGetAnswer()
    {
        // Create test models
        $field = Field::factory()
            ->for($this->subsidyStage)
            ->create();

        $appStage = ApplicationStage::factory()->create();
        Answer::factory()->create([
            'application_stage_id' => $appStage->id,
            'field_id' => $field->id,
        ]);

        $foundAnswer = $this->repository->getAnswer($appStage, $field);
        $this->assertInstanceOf(Answer::class, $foundAnswer);

        $field2 = Field::factory()->create(['subsidy_stage_id' => $this->subsidyStage->id]);
        $this->assertNull($this->repository->getAnswer($appStage, $field2));
    }

    public function testMakeApplicationForSubsidyVersion()
    {
        // Test make application for subsidy version
        $application = $this->repository->makeApplicationForIdentityAndSubsidyVersion(
            $this->identity,
            $this->subsidyVersion
        );
        $this->assertInstanceOf(Application::class, $application);
        $this->assertEquals($this->subsidyVersion->id, $application->subsidy_version_id);
    }

    public function testMakeApplicationStage()
    {
        // Create test models
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create();
        $subsidyStage = SubsidyStage::factory()->create(['subsidy_version_id' => $this->subsidyVersion->id]);

        // Test make application stage
        $applicationStage = $this->repository->makeApplicationStage($application, $subsidyStage);
        $this->assertInstanceOf(ApplicationStage::class, $applicationStage);
        $this->assertEquals($application->id, $applicationStage->application->id);
        $this->assertEquals($subsidyStage->id, $applicationStage->subsidy_stage_id);
    }

    public function testMakeAnswer()
    {
        // Create test models
        $field = Field::factory()->create(['subsidy_stage_id' => $this->subsidyStage->id]);
        $applicationStage = ApplicationStage::factory()->create();

        // Test make answer
        $answer = $this->repository->makeAnswer($applicationStage, $field);
        $this->assertInstanceOf(Answer::class, $answer);
        $this->assertEquals($applicationStage->id, $answer->applicationStage->id);
        $this->assertEquals($field->id, $answer->field_id);
    }

    public function testSaveApplication()
    {
        // Create a test application
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->make();

        // Test save application
        $this->repository->saveApplication($application);

        $this->assertDatabaseHas('applications', ['id' => $application->id]);
    }

    public function testSaveApplicationStage()
    {
        // Create a test application stage
        $applicationStage = ApplicationStage::factory()->for($this->subsidyStage)->make();

        // Test save application stage
        $this->repository->saveApplicationStage($applicationStage);

        $this->assertDatabaseHas('application_stages', ['id' => $applicationStage->id]);
    }

    public function testSaveAnswer()
    {
        // Create a test answer
        $answer = Answer::factory()->make();
        // Test save answer
        $this->repository->saveAnswer($answer);

        $this->assertDatabaseHas('answers', ['id' => $answer->id]);
    }

    public function testGetAnswersForApplicationStagesUpToIncluding(): void
    {
        $stage1Field1 = Field::factory()->create(['subsidy_stage_id' => $this->subsidyStage->id]);
        $stage1Field2 = Field::factory()->create(['subsidy_stage_id' => $this->subsidyStage->id]);

        $subsidyStage2 = SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'stage' => 2
        ]);

        $stage2Field1 = Field::factory()->create(['subsidy_stage_id' => $subsidyStage2->id]);

        $application = Application::factory()->create([
            'identity_id' => $this->identity->id,
            'subsidy_version_id' => $this->subsidyVersion->id,
            'updated_at' => Carbon::today(),
            'created_at' => Carbon::today(),
            'final_review_deadline' => Carbon::today(),
        ]);

        $applicationStage1 = ApplicationStage::factory()->create([
            'application_id' => $application->id,
            'subsidy_stage_id' => $this->subsidyStage->id,
            'sequence_number' => 1,
            'is_current' => false,
            'is_submitted' => true
        ]);

        Answer::factory()->create([
            'application_stage_id' => $applicationStage1->id,
            'field_id' => $stage1Field1->id
        ]);

        Answer::factory()->create([
            'application_stage_id' => $applicationStage1->id,
            'field_id' => $stage1Field2->id
        ]);

        $applicationStage2 = ApplicationStage::factory()->create([
            'application_id' => $application->id,
            'subsidy_stage_id' => $subsidyStage2->id,
            'sequence_number' => 2,
            'is_current' => true
        ]);

        Answer::factory()->create([
            'application_stage_id' => $applicationStage2->id,
            'field_id' => $stage2Field1->id
        ]);

        $answers = $this->repository->getAnswersForApplicationStagesUpToIncluding($applicationStage1);
        $this->assertCount(1, $answers->stages);
        $this->assertCount(2, $answers->stages[0]->answers);
        $this->assertEquals($applicationStage1->id, $answers->stages[0]->stage->id);

        $answers = $this->repository->getAnswersForApplicationStagesUpToIncluding($applicationStage2);
        $this->assertCount(2, $answers->stages);
        $this->assertCount(2, $answers->stages[0]->answers);
        $this->assertEquals($applicationStage1->id, $answers->stages[0]->stage->id);
        $this->assertCount(1, $answers->stages[1]->answers);
        $this->assertEquals($applicationStage2->id, $answers->stages[1]->stage->id);
    }

    /**
     * @group clone-answers
     */
    public function testCloningAnswers(): void
    {
        $field = Field::factory()
            ->for($this->subsidyStage)
            ->create();

        $applicationStageSource = ApplicationStage::factory()
            ->for($this->subsidyStage)
            ->create([
                'sequence_number' => 1,
            ]);

        $answerSource = Answer::factory()
            ->for($field)
            ->for($applicationStageSource)
            ->create();

        $applicationStageTarget = ApplicationStage::factory()
            ->for($this->subsidyStage)
            ->create([
                'sequence_number' => 2,
            ]);

        $this->repository->cloneApplicationStageAnswers($applicationStageSource, $applicationStageTarget);

        $this->assertEquals(
            $answerSource->encrypted_answer,
            $applicationStageTarget->answers()->first()->encrypted_answer
        );
    }

    /**
     * @group clone-answers
     */
    public function testCloningAnswersShouldTakeExcludedFieldsIntoAccount(): void
    {
        $field = Field::factory()
            ->for($this->subsidyStage)
            ->create([
                'exclude_from_clone_data' => true,
            ]);


        $applicationStageSource = ApplicationStage::factory()
            ->for($this->subsidyStage)
            ->create([
                'sequence_number' => 1,
            ]);

        $answer = Answer::factory()
            ->for($field)
            ->for($applicationStageSource)
            ->create();

        $applicationStageTarget = ApplicationStage::factory()
            ->for($this->subsidyStage)
            ->create([
                'sequence_number' => 2,
            ]);

        $this->repository->cloneApplicationStageAnswers($applicationStageSource, $applicationStageTarget);

        $this->assertCount(0, $applicationStageTarget->answers);
    }

    public function testGetAllApplicationsSorted()
    {
        Carbon::setTestNow(Carbon::now());

        $firstApplication = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->withApplicantStage($this->subsidyStage)
            ->create([
                'application_title' => 'First Application',
                'final_review_deadline' => Carbon::now()->addDays(1)->startOfDay(),
                'updated_at' => Carbon::now(),
                'status' => ApplicationStatus::Pending,
            ]);

        $secondApplication = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->withApplicantStage($this->subsidyStage)
            ->create([
                'application_title' => 'Second Application',
                'final_review_deadline' => Carbon::now()->addDays(2)->startOfDay(),
                'updated_at' => Carbon::now(),
                'status' => ApplicationStatus::Pending,
            ]);

        $user = User::factory()->create();
        $user->attachRole(Role::Assessor);

        $appFilter = new ApplicationsFilter();

        $applicationsPaginated = $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $appFilter,
            paginationOptions: new PaginationOptions(1, 15),
            sortOptions: new SortOptions([
                new SortColumn('final_review_deadline', true),
            ]),
        );

        /** @var Application[] $applications */
        $applications = $applicationsPaginated->items();
        $this->assertSame($firstApplication->application_title, $applications[0]->application_title);
        $this->assertSame($secondApplication->application_title, $applications[1]->application_title);

        $applicationsPaginated = $this->repository->filterApplicationsPaginated(
            user: $user,
            onlyMyApplications: false,
            filter: $appFilter,
            paginationOptions: new PaginationOptions(1, 15),
            sortOptions: new SortOptions([
                new SortColumn('final_review_deadline', false),
            ]),
        );


        /** @var Application[] $applications */
        $applications = $applicationsPaginated->items();
        $this->assertSame($firstApplication->application_title, $applications[1]->application_title);
        $this->assertSame($secondApplication->application_title, $applications[0]->application_title);
    }


    public function testGetAnswersForApplicationStagesUpToIncludingWithReadOnly(): void
    {
        $stage1Field1 = Field::factory()->create(['subsidy_stage_id' => $this->subsidyStage->id]);
        $stage1Field2 = Field::factory()->create(['subsidy_stage_id' => $this->subsidyStage->id]);

        $subsidyStage2 = SubsidyStage::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'stage' => 2,
            ]
        );

        $stage2Field1 = Field::factory()->create(['subsidy_stage_id' => $subsidyStage2->id]);

        $application = Application::factory()->create(
            [
                'identity_id' => $this->identity->id,
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
            ]
        );

        $applicationStage1 = ApplicationStage::factory()->create(
            [
                'application_id' => $application->id,
                'subsidy_stage_id' => $this->subsidyStage->id,
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
            ]
        );

        Answer::factory()->create(
            [
                'application_stage_id' => $applicationStage1->id,
                'field_id' => $stage1Field1->id,
            ]
        );

        Answer::factory()->create(
            [
                'application_stage_id' => $applicationStage1->id,
                'field_id' => $stage1Field2->id,
            ]
        );

        $applicationStage2 = ApplicationStage::factory()->create(
            [
                'application_id' => $application->id,
                'subsidy_stage_id' => $subsidyStage2->id,
                'sequence_number' => 2,
                'is_current' => true,
            ]
        );

        Answer::factory()->create(
            [
                'application_stage_id' => $applicationStage2->id,
                'field_id' => $stage2Field1->id,
                'encrypted_answer' => json_encode('this should not be visible'),
            ]
        );

        $answers = $this->repository->getAnswersForApplicationStagesUpToIncluding($applicationStage1);
        $this->assertCount(1, $answers->stages);
        $this->assertCount(2, $answers->stages[0]->answers);
        $this->assertEquals($applicationStage1->id, $answers->stages[0]->stage->id);

        $answers = $this->repository->getAnswersForApplicationStagesUpToIncluding($applicationStage2, true);
        $this->assertCount(1, $answers->stages);
        $this->assertCount(2, $answers->stages[0]->answers);
        $this->assertEquals($applicationStage1->id, $answers->stages[0]->stage->id);
        $this->assertArrayNotHasKey(1, $answers->stages);
    }
}
