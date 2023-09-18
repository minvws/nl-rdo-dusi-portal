<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;

class ModelApplicationTest extends TestCase
{
    use DatabaseTransactions;

    private Application $application;
    private ApplicationStage $applicationStage;
    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyVersion $falseSubsidyVersion;
    private Application $falseApplication;


    protected function setUp(): void
    {
        parent::setUp();
        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();

        $this->subsidy = Subsidy::factory()->create([
            'title' => 'Test Subsidy',
        ]);
        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy->id,
        ]);
        $this->application = Application::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'application_title' => 'Test Application',
            'created_at' => '2021-01-01',
            'final_review_deadline' => '2021-01-01',
            'updated_at' => '2021-01-01',
            'status' => ApplicationStatus::Submitted,
        ]);

        $this->applicationStage = ApplicationStage::factory()->create([
            'application_id' => $this->application->id,
        ]);
        $this->falseSubsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => Subsidy::factory()->create()->id,
        ]);
        $this->falseApplication = Application::factory()->create();
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
        $query = Application::query()->status(ApplicationStatus::Submitted)->get();

        $this->assertTrue($query->contains('id', $this->application->id));

        $this->assertFalse($query->contains('id', $this->falseApplication->id));
    }

    public function testScopeSubsidyTitle()
    {
        $query = Application::query()->subsidyTitle('Test Subsidy')->get();

        $this->assertTrue($query->contains('subsidy_version_id', $this->subsidyVersion->id));

        $this->assertFalse($query->contains('subsidy_version_id', $this->falseSubsidyVersion->id));
    }
}
