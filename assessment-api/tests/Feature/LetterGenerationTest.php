<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

use Illuminate\Support\Facades\Queue;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Events\ApplicationMessageEvent;
use MinVWS\DUSi\Shared\Application\Jobs\GenerateLetterJob;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class LetterGenerationTest extends TestCase
{
    private SubsidyStageTransitionMessage $message;
    private ApplicationStageTransition $applicationStageTransition;

    protected function setUp(): void
    {
        parent::setUp();

        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->for($subsidy)->create();
        $subsidyStage1 = SubsidyStage::factory()->for($subsidyVersion)->create(['stage' => 1]);
        $subsidyStage2 = SubsidyStage::factory()->for($subsidyVersion)->create(['stage' => 2]);
        $subsidyStageTransition =
            SubsidyStageTransition::factory()
                ->for($subsidyStage1, 'currentSubsidyStage')
                ->for($subsidyStage2, 'targetSubsidyStage')
                ->create();
        $this->subsidyStageMessage = SubsidyStageTransitionMessage::factory()->for($subsidyStageTransition)->create();

        $application = Application::factory()->for($subsidyVersion)->create();
        $applicationStage1 =
            ApplicationStage::factory()
                ->for($application)
                ->for($subsidyStage1)
                ->create(['sequence_number' => 1]);
        $applicationStage2 =
            ApplicationStage::factory()
                ->for($application)
                ->for($subsidyStage2)
                ->create(['sequence_number' => 2]);
        $this->applicationStageTransition =
            ApplicationStageTransition::factory()
                ->for($application)
                ->for($subsidyStageTransition)
                ->for($applicationStage1, 'previousApplicationStage')
                ->for($applicationStage2, 'newApplicationStage')
                ->create();
    }

    public function testApplicationDecidedEventTriggerGenerateLetterJob(): void
    {
        Queue::fake();
        ApplicationMessageEvent::dispatch($this->subsidyStageMessage, $this->applicationStageTransition);
        Queue::assertPushed(GenerateLetterJob::class);
    }
}
