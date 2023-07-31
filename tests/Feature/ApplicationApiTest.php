<?php

namespace Tests\Feature;

use Carbon\Carbon;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Tests\TestCase;

class ApplicationApiTest extends TestCase
{
    public function testFilterByFinalReviewDeadline()
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $subsidy->id,
        ]);
        $application = Application::factory()->create(
            [
                'subsidy_version_id' => $subsidyVersion->id,
            ]
        );
        $applicationStage = ApplicationStage::factory()->create(
            [
                'application_id' => $application->id,
            ]
        );

        $filters = [
            'final_review_deadline' => Carbon::now()->toDateString(),
            'status' => $applicationStage->status->value,
            'user_id' => $applicationStage->user_id,
            'subsidy_title' => $subsidyVersion->subsidy->title,
            'application_title' => $application->application_title,
        ];

        $response = $this->json('GET', '/api/applicationsFilter', $filters);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'application_title' => $application->application_title,
            'id' => $application->applicationStages()->where('status', $applicationStage->status->value)->first()->application_id,
            'subsidy_version_id' => $subsidyVersion->id
        ]);

    }
}
