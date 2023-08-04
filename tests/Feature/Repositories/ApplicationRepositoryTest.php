<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Tests\Feature\Repositories;

use Illuminate\Database\QueryException;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Application\Tests\Feature\TestCase;

use function Laravel\Prompts\error;

class ApplicationRepositoryTest extends TestCase
{
    private ApplicationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ApplicationRepository();
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
        $applicationStageVersion = ApplicationStageVersion::factory()->create();
        $field = Field::factory()->create();
        Answer::factory()->create([
            'application_stage_version_id' => $applicationStageVersion->id,
            'field_id' => $field->id,
        ]);

        $foundAnswer = $this->repository->getAnswer($applicationStageVersion, $field);
        $this->assertInstanceOf(Answer::class, $foundAnswer);

        $this->assertNull($this->repository->getAnswer($applicationStageVersion, Field::factory()->create()));
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
        $applicationStageVersion = ApplicationStageVersion::factory()->create();
        $field = Field::factory()->create();

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
        error($answer->id);

        // Test save answer
        $this->repository->saveAnswer($answer);

        $this->assertDatabaseHas('answers', ['id' => $answer->id]);
    }
}
