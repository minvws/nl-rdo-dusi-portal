<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Connection;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Tests\TestCase;

class ApplicationApiTest extends TestCase
{
    use DatabaseTransactions;

    protected array $connectionsToTransact = [Connection::APPLICATION, Connection::FORM];
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
        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy->id,
        ]);
        $this->application = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'created_at' => Carbon::today()->toDateString(),
                'final_review_deadline' => Carbon::tomorrow()->toDateString(),
            ]
        );
        $this->applicationStage = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application->id,
            ]
        );
        $this->applicationStageVersion = ApplicationStageVersion::factory()->create(
            [
                'application_stage_id' => $this->applicationStage->id,
            ]
        );
    }
    public function testFilter()
    {
        $filters = [
            'application_title' => $this->application->application_title,
            'date_from' => $this->application->created_at->toDateString(),
            'date_to' => Carbon::tomorrow()->toDateString(),
            'date_last_modified_from' => $this->application->updated_at->toDateString(),
            'date_last_modified_to' => Carbon::tomorrow()->toDateString(),
            'date_final_review_deadline_from' => Carbon::yesterday()->toDateString(),
            'date_final_review_deadline_to' => Carbon::tomorrow()->toDateString(),
            'status' => $this->applicationStageVersion->status,
            'subsidy' => $this->subsidy->title,
        ];

        $response = $this->json('GET', '/api/applicationsFilter', $filters);

        $response->assertStatus(200);


        $response->assertJsonFragment([
            'application_title' => $this->application->application_title,
            'subsidy' => $this->subsidy->title,
            'status' => $this->applicationStageVersion->status,
            'final_review_deadline' => $this->application->final_review_deadline,
            'updated_at' => $this->application->updated_at,
        ]);
    }

    public static function noResultFilterProvider(): \Generator
    {
        yield [
            [
                'application_title' => 'test',
            ],
        ];
        yield [
            [
                'date_from' => Carbon::tomorrow()->toDateString(),
            ],
        ];
        yield [
            [
                'date_to' => Carbon::yesterday()->toDateString(),
            ],
        ];
        yield [
            [
                'date_last_modified_from' => Carbon::tomorrow()->addDays(20)->toDateString(),
            ],
        ];
        yield [
            [
                'date_last_modified_to' => Carbon::yesterday()->toDateString(),
            ],
        ];
        yield [
            [
                'date_final_review_deadline_from' => Carbon::tomorrow()->addDays(2)->toDateString(),
            ],
        ];
        yield [
            [
                'date_final_review_deadline_to' => Carbon::yesterday()->toDateString(),
            ],
        ];
        yield [
            [
                'status' => 'test',
            ],
        ];
        yield [
            [
                'subsidy' => 'test',
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
        $response->assertContent('{"data":[]}');
    }
}
