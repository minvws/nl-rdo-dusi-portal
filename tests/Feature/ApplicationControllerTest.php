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

class ApplicationControllerTest extends TestCase
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
            'date_from' => $this->application->created_at,
            'date_to' => $this->application->created_at,
            'date_last_modified_from' => $this->application->updated_at,
            'date_last_modified_to' => $this->application->updated_at,
            'date_final_review_deadline_from' => $this->application->final_review_deadline,
            'date_final_review_deadline_to' => $this->application->final_review_deadline,
            'status' => $this->applicationStageVersion->status,
            'subsidy' => $this->subsidy->title,
        ];

        $response = $this->json('GET', '/api/applications', $filters);

        $response->assertStatus(200);


        $response->assertJsonFragment([
            'application_title' => $this->application->application_title,
            'subsidy' => $this->subsidy->title,
            'status' => 'ToBeDetermined',
            'final_review_deadline' => $this->application->final_review_deadline,
            'updated_at' => $this->application->updated_at,
        ]);
    }

    public static function noResultFilterProvider(): \Generator
    {
        yield [
            [
                'application_title' => "123test123",
            ],
            200,
            '{"data":[]}',
        ];
        yield [
            [
                'date_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[]}',
        ];
        yield [
            [
                'date_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[]}',
        ];
        yield [
            [
                'date_last_modified_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[]}',
        ];
        yield [
            [
                'date_last_modified_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[]}',
        ];
        yield [
            [
                'date_final_review_deadline_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[]}',
        ];
        yield [
            [
                'date_final_review_deadline_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[]}',
        ];
        yield [
            [
                'status' => 'test',
            ],
            422,
            '{"message":"The selected status is invalid.","errors":{"status":["The selected status is invalid."]}}',
        ];
        yield [
            [
                'subsidy' => 'test',
            ],
            200,
            '{"data":[]}',
        ];
    }

    /**
     * @dataProvider noResultFilterProvider
     */
    public function testNoResultFilter(mixed $filters, mixed $status, mixed $content)
    {
        $response = $this->json('GET', '/api/applications', $filters);
        $response->assertStatus($status);
        $response->assertContent($content);
    }
}
