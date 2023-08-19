<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Tests\Feature\Repositories;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Application\Tests\Feature\TestCase;

class ApplicationRepositoryTest extends TestCase
{
    private ApplicationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ApplicationRepository();
    }

    /**
     * @throws \Exception
     */
    public function testGetApplicationWith()
    {
        $subsidy = Subsidy::factory()->create(
            [
                'title' => 'some_subsidy_title',
            ]
        );
        $subsidyVersion = SubsidyVersion::factory()->create(
            [
                'subsidy_id' => $subsidy->id,
            ]
        );
        // Create a test application
        $application = Application::factory()->create(
            [
                'application_title' => 'some_application_title',
                'updated_at' => new \DateTime('now'),
                'created_at' => new \DateTime('now'),
                'final_review_deadline' => new \DateTime('now'),
                'subsidy_version_id' => $subsidyVersion->id,
            ]
        );
        $appStageId = ApplicationStage::factory()->create(
            [
                'application_id' => $application->id,
            ]
        )->id;
        ApplicationStageVersion::factory()->create(
            [
                'application_stage_id' => $appStageId,
                'status' => ApplicationStageVersionStatus::Submitted,
            ]
        )->id;

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
            'status' => ApplicationStageVersionStatus::Submitted,
            'subsidy' => 'some_subsidy_title',
        ];
        $appFilter = ApplicationsFilter::fromArray($filter);
        // Test valid application
        $foundApplication = $this->repository->filterApplications($appFilter);
        $this->assertInstanceOf(Application::class, $foundApplication->first());

        // Test invalid application
        $filter = [
            'application_title' => 'invalid_application_title',
        ];

        $appFilter = ApplicationsFilter::fromArray($filter);

        $foundApplication = $this->repository->filterApplications($appFilter);
        $this->assertEmpty($foundApplication);
    }

    public function testGetApplication()
    {
        // Create a test application
        $application = Application::factory()->create();

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

    public function testGetApplicationStageVersion()
    {
        // Create a test application stage version
        $applicationStageVersion = ApplicationStageVersion::factory()->create();

        // Test valid application stage version
        $foundApplicationStageVersion = $this->repository->getApplicationStageVersion($applicationStageVersion->id);
        $this->assertInstanceOf(ApplicationStageVersion::class, $foundApplicationStageVersion);

        // Test invalid application stage version
        $this->expectException(QueryException::class);
        $this->repository->getApplicationStageVersion('invalid_application_stage_version_id');
    }

    public function testGetAnswer()
    {
        // Create test models
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create(['subsidy_id' => $subsidy->id]);
        $subsidyStage = SubsidyStage::factory()->create(['subsidy_version_id' => $subsidyVersion->id]);
        $field = Field::factory()->create(['subsidy_stage_id' => $subsidyStage->id]);
        $applicationStageVersion = ApplicationStageVersion::factory()->create();
        Answer::factory()->create([
            'application_stage_version_id' => $applicationStageVersion->id,
            'field_id' => $field->id,
        ]);

        $foundAnswer = $this->repository->getAnswer($applicationStageVersion, $field);
        $this->assertInstanceOf(Answer::class, $foundAnswer);

        $field2 = Field::factory()->create(['subsidy_stage_id' => $subsidyStage->id]);
        $this->assertNull($this->repository->getAnswer($applicationStageVersion, $field2));
    }

    public function testMakeApplicationForSubsidyVersion()
    {
        // Create a test subsidy version
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create(['subsidy_id' => $subsidy->id]);

        // Test make application for subsidy version
        $application = $this->repository->makeApplicationForSubsidyVersion($subsidyVersion);
        $this->assertInstanceOf(Application::class, $application);
        $this->assertEquals($subsidyVersion->id, $application->subsidy_version_id);
    }

    public function testMakeApplicationStage()
    {
        // Create test models
        $application = Application::factory()->create();
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create(['subsidy_id' => $subsidy->id]);
        $subsidyStage = SubsidyStage::factory()->create(['subsidy_version_id' => $subsidyVersion->id]);

        // Test make application stage
        $applicationStage = $this->repository->makeApplicationStage($application, $subsidyStage);
        $this->assertInstanceOf(ApplicationStage::class, $applicationStage);
        $this->assertEquals($application->id, $applicationStage->application->id);
        $this->assertEquals($subsidyStage->id, $applicationStage->subsidy_stage_id);
    }

    public function testMakeApplicationStageVersion()
    {
        // Create test models
        $applicationStage = ApplicationStage::factory()->create();

        // Test make application stage version
        $applicationStageVersion = $this->repository->makeApplicationStageVersion($applicationStage);
        $this->assertInstanceOf(ApplicationStageVersion::class, $applicationStageVersion);
        $this->assertEquals($applicationStage->id, $applicationStageVersion->applicationStage->id);
    }

    public function testMakeAnswer()
    {
        // Create test models
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create(['subsidy_id' => $subsidy->id]);
        $subsidyStage = SubsidyStage::factory()->create(['subsidy_version_id' => $subsidyVersion->id]);
        $field = Field::factory()->create(['subsidy_stage_id' => $subsidyStage->id]);
        $applicationStageVersion = ApplicationStageVersion::factory()->create();

        // Test make answer
        $answer = $this->repository->makeAnswer($applicationStageVersion, $field);
        $this->assertInstanceOf(Answer::class, $answer);
        $this->assertEquals($applicationStageVersion->id, $answer->applicationStageVersion->id);
        $this->assertEquals($field->id, $answer->field_id);
    }

    public function testSaveApplication()
    {
        // Create a test application
        $application = Application::factory()->make();

        // Test save application
        $this->repository->saveApplication($application);

        $this->assertDatabaseHas('applications', ['id' => $application->id]);
    }

    public function testSaveApplicationStageVersion()
    {
        // Create a test application stage version
        $applicationStageVersion = ApplicationStageVersion::factory()->make();

        // Test save application stage version
        $this->repository->saveApplicationStageVersion($applicationStageVersion);

        $this->assertDatabaseHas('application_stage_versions', ['id' => $applicationStageVersion->id]);
    }

    public function testSaveApplicationStage()
    {
        // Create a test application stage
        $applicationStage = ApplicationStage::factory()->make();

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

    public function testGetLatestApplicationStageVersion()
    {
        // Create test models
        $applicationStage = ApplicationStage::factory()->create();
        $now = Carbon::now();
        $expectedApplicationStageVersion = ApplicationStageVersion::factory()->create([
            'application_stage_id' => $applicationStage->id,
            'created_at' => $now,
            'version' => '3',
        ]);
        ApplicationStageVersion::factory()->create([
            'application_stage_id' => $applicationStage->id,
            'created_at' => $now->modify('+1 day'),
            'version' => '2',
        ]);
        ApplicationStageVersion::factory()->create([
            'application_stage_id' => $applicationStage->id,
            'created_at' => $now->modify('+2 day'),
            'version' => '1',
        ]);

        // Test get latest application stage version
        $latestApplicationStageVersion = $this->repository->getLatestApplicationStageVersion($applicationStage);
        $this->assertInstanceOf(ApplicationStageVersion::class, $latestApplicationStageVersion);
        $this->assertEquals($expectedApplicationStageVersion->id, $latestApplicationStageVersion->id);
        $this->assertEquals($expectedApplicationStageVersion->id, $applicationStage->latestApplicationStageVersion->id);
    }

    public function testGetAnswersForApplicationStagesUpToIncluding(): void
    {
        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();
        ApplicationStageVersion::query()->truncate();

        $subsidy = Subsidy::factory()->create();

        $subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $subsidy->id
        ]);

        $subsidyStage1 = SubsidyStage::factory()->create([
            'subsidy_version_id' => $subsidyVersion->id,
            'stage' => 1
        ]);

        $stage1Field1 = Field::factory()->create(['subsidy_stage_id' => $subsidyStage1->id]);
        $stage1Field2 = Field::factory()->create(['subsidy_stage_id' => $subsidyStage1->id]);

        $subsidyStage2 = SubsidyStage::factory()->create([
            'subsidy_version_id' => $subsidyVersion->id,
            'stage' => 2
        ]);

        $stage2Field1 = Field::factory()->create(['subsidy_stage_id' => $subsidyStage2->id]);

        $application = Application::factory()->create([
            'subsidy_version_id' => $subsidyVersion->id,
            'updated_at' => Carbon::today(),
            'created_at' => Carbon::today(),
            'final_review_deadline' => Carbon::today(),
        ]);

        $applicationStage1 = ApplicationStage::factory()->create([
            'application_id' => $application->id,
            'subsidy_stage_id' => $subsidyStage1->id,
            'stage' => $subsidyStage1->stage
        ]);

        $applicationStage1Version = ApplicationStageVersion::factory()->create([
            'application_stage_id' => $applicationStage1->id,
        ]);

        Answer::factory()->create([
            'application_stage_version_id' => $applicationStage1Version->id,
            'field_id' => $stage1Field1->id
        ]);

        Answer::factory()->create([
            'application_stage_version_id' => $applicationStage1Version->id,
            'field_id' => $stage1Field2->id
        ]);

        $applicationStage2 = ApplicationStage::factory()->create([
            'application_id' => $application->id,
            'subsidy_stage_id' => $subsidyStage2->id,
            'stage' => $subsidyStage2->stage
        ]);

        $applicationStage2Version = ApplicationStageVersion::factory()->create([
            'application_stage_id' => $applicationStage2->id,
        ]);

        Answer::factory()->create([
            'application_stage_version_id' => $applicationStage2Version->id,
            'field_id' => $stage2Field1->id
        ]);

        $answers = $this->repository->getAnswersForApplicationStagesUpToIncluding($applicationStage1Version);
        $this->assertCount(1, $answers->stages);
        $this->assertCount(2, $answers->stages[0]->answers);
        $this->assertEquals($applicationStage1Version->id, $answers->stages[0]->stageVersion->id);
        $this->assertEquals($applicationStage1->id, $answers->stages[0]->stage->id);

        $answers = $this->repository->getAnswersForApplicationStagesUpToIncluding($applicationStage2Version);
        $this->assertCount(2, $answers->stages);
        $this->assertCount(2, $answers->stages[0]->answers);
        $this->assertEquals($applicationStage1Version->id, $answers->stages[0]->stageVersion->id);
        $this->assertEquals($applicationStage1->id, $answers->stages[0]->stage->id);
        $this->assertCount(1, $answers->stages[1]->answers);
        $this->assertEquals($applicationStage2Version->id, $answers->stages[1]->stageVersion->id);
        $this->assertEquals($applicationStage2->id, $answers->stages[1]->stage->id);
    }
}
