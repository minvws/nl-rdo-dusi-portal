<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

class ApplicationControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected array $connectionsToTransact = [Connection::APPLICATION];
    private Application $application1;
    private ApplicationStage $applicationStage1;
    private Application $application2;
    private ApplicationStage $applicationStage2;
    private Application $application3;
    private ApplicationStage $applicationStage3;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage;
    private Authenticatable $user1;
    private Authenticatable $user2;

    protected function setUp(): void
    {
        parent::setUp();

        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();

        $this->user1 = User::factory()->create();
        $this->user1->attachRole(RoleEnum::ImplementationCoordinator);
        $this->user2 = User::factory()->create();
        $this->user2->attachRole(RoleEnum::Assessor);
        $this->user3 = User::factory()->create();
        $this->user3->attachRole(RoleEnum::Assessor);


        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy->id,
        ]);

        $this->subsidyStage = SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'assessor_user_role' => RoleEnum::Assessor->value,
        ]);

        $this->subsidyStage = SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'assessor_user_role' => RoleEnum::InternalAuditor->value,
        ]);

        $this->application1 = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
            ]
        );
        $this->applicationStage1 = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application1->id,
            ]
        );

        $this->application2 = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
            ]
        );
        $this->applicationStage2 = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application2->id,
                'assessor_user_id' => $this->user2,
            ]
        );
        $this->application2 = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
            ]
        );
        $this->applicationStage2 = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application2->id,
                'assessor_user_id' => $this->user2,
            ]
        );
        $this->application3 = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
            ]
        );
        $this->applicationStage3 = ApplicationStage::factory()->create(
            [
                'application_id' => $this->application3->id,
                'assessor_user_id' => $this->user3,
            ]
        );


        $this->user1 = User::factory()->create();
        $this->user1->attachRole(RoleEnum::ImplementationCoordinator);
    }


    public function testFilter(): void
    {
        $filters = [
            'application_title' => $this->application1->application_title,
            'date_from' => $this->application1->created_at,
            'date_to' => $this->application1->created_at,
            'date_last_modified_from' => $this->application1->updated_at,
            'date_last_modified_to' => $this->application1->updated_at,
            'date_final_review_deadline_from' => $this->application1->final_review_deadline,
            'date_final_review_deadline_to' => $this->application1->final_review_deadline,
            'status' => $this->application1->status->value,
            'subsidy' => $this->subsidy->title,
        ];

        $response = $this
            ->be($this->user1)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'application_title' => $this->application1->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application1->status->value,
            'final_review_deadline' => $this->application1->final_review_deadline,
            'updated_at' => $this->application1->updated_at,
            'actions' => ['release', 'show']
        ]);
    }

    public function testList(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::ImplementationCoordinator);

        $response = $this
            ->be($this->user1)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'application_title' => $this->application1->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application1->status->value,
            'final_review_deadline' => $this->application1->final_review_deadline,
            'updated_at' => $this->application1->updated_at,
            'actions' => ['release', 'show'],
        ]);
        $response->assertJsonFragment([
            'application_title' => $this->application2->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application2->status->value,
            'final_review_deadline' => $this->application2->final_review_deadline,
            'updated_at' => $this->application2->updated_at,
            'actions' => ['release', 'show'],
        ]);
    }

    public function testListAsAssessor(): void
    {
        $response = $this
            ->be($this->user2)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'application_title' => $this->application2->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application2->status->value,
            'final_review_deadline' => $this->application2->final_review_deadline,
            'updated_at' => $this->application2->updated_at,
            'actions' => ['release', 'show'],
        ]);

        // Don't show the one where you are not the assessor
        $response->assertJsonMissing([
            'application_title' => $this->application3->application_title,
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
    public function testNoResultFilter(mixed $filters, mixed $status, mixed $content): void
    {
        $response = $this
            ->be($this->user1)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus($status);
        $response->assertContent($content);
    }
}
