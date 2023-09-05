<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use MinVWS\DUSi\Assessment\API\Jobs\GenerateLetterJob;
use MinVWS\DUSi\Assessment\API\Models\Connection;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Events\ApplicationStageVersionDecidedEvent;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class LetterGenerationTest extends TestCase
{
    use DatabaseTransactions;

    protected array $connectionsToTransact = [Connection::APPLICATION];
    private Application $application;
    private ApplicationStage $applicationStage;
    protected function setUp(): void
    {
        parent::setUp();

        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();
        ApplicationStageVersion::query()->truncate();

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()->create(
            [
                'subsidy_id' => $this->subsidy->id,
            ]
        );
        $this->application = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
            ]
        );
        $this->applicationStage = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application->id,
            ]
        );
    }

    public function testApplcationDecidedEventTriggerGenerateLetterJob(): void
    {
        Queue::fake();

        $applicationStageVersion = ApplicationStageVersion::factory()->create(
            [
                'application_stage_id' => $this->applicationStage->id,
            ]
        );

        ApplicationStageVersionDecidedEvent::dispatch($applicationStageVersion);
        Queue::assertPushed(GenerateLetterJob::class);
    }
}
