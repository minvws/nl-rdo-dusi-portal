<?php

namespace Tests\Feature;

use Carbon\Carbon;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ApplicationApiTest extends TestCase
{
    private Application $application;
    private ApplicationStage $applicationStage;
    protected function setUp(): void
    {
        parent::setUp();
        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();

        $subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $subsidy->id,
        ]);
        $this->application = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'final_review_deadline' => Carbon::tomorrow()->toDateString(),
            ]
        );
        $this->applicationStage = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application->id,
            ]
        );
    }
    public function testFilter()
    {
        $filters = [
            'final_review_deadline' => Carbon::today()->toDateString(),
            'status' => $this->applicationStage->status->value,
            'user_id' => $this->applicationStage->user_id,
            'subsidy_title' => $this->subsidyVersion->subsidy->title,
            'application_title' => $this->application->application_title,
        ];

        $response = $this->json('GET', '/api/applicationsFilter', $filters);

        $response->assertStatus(200);

        assert($response->json()[0]['final_review_deadline'] === Carbon::tomorrow()->toISOString());

        $response->assertJsonFragment([
            'application_title' => $this->application->application_title,
            'id' => $this->application->applicationStages()->where('status', $this->applicationStage->status->value)->first()->application_id,
            'subsidy_version_id' => $this->subsidyVersion->id,
            'final_review_deadline' => Carbon::tomorrow()->toISOString(),
        ]);
    }

    public static function noResultFilterProvider(): \Generator
    {
        yield [
            [
                'final_review_deadline' => Carbon::tomorrow()->addDays(2)->toDateString(),
            ],
        ];
        yield [
            [
                'status' => 'test',
            ],
        ];
        yield [
            [
                'user_id' => Uuid::uuid4(),
            ],
        ];
        yield [
            [
                'subsidy_title' => 'test123123',
            ],
        ];
        yield [
            [
                'application_title' => 'test',
            ],
        ];
    }

    /**
     * @dataProvider noResultFilterProvider
     */
    public function testNoResultFilter(mixed $filters)
    {
        $response = $this->json('GET', '/api/applicationsFilter', $filters);
        $response->assertStatus(200);
        $response->assertContent('[]');
    }
}
