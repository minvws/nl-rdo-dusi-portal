<?php

declare(strict_types=1);

namespace Feature\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @group application-list
 */
class ApplicationControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected array $connectionsToTransact = [Connection::APPLICATION];
    private Application $application1;
    private ApplicationStage $application1Stage1;

    private Application $application2;
    private ApplicationStage $application2Stage1;
    private ApplicationStage $application2Stage2;

    private Application $application3;
    private ApplicationStage $application3Stage1;
    private ApplicationStage $application3Stage2;

    private Application $application4;
    private ApplicationStage $application4Stage1;
    private ApplicationStage $application4Stage2;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStage $subsidyStage2;

    private Authenticatable $implementationCoordinatorUser;
    private Authenticatable $assessorUser1;


    protected function setUp(): void
    {
        parent::setUp();

        Subsidy::query()->truncate();
        SubsidyVersion::query()->truncate();
        Application::query()->truncate();
        ApplicationStage::query()->truncate();

        $this->subsidy = Subsidy::factory()->create();

        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy->id,
        ]);

        $this->subsidyStage1 = SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
        ]);

        $this->subsidyStage2 = SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'assessor_user_role' => RoleEnum::Assessor->value,
            'subject_role' => SubjectRole::Assessor
        ]);

        $this->subsidyStage3 = SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'assessor_user_role' => RoleEnum::ImplementationCoordinator->value,
            'subject_role' => SubjectRole::Assessor
        ]);

        $this->implementationCoordinatorUser = User::factory()->create();
        $this->implementationCoordinatorUser->attachRole(RoleEnum::ImplementationCoordinator, $this->subsidy->id);
        $this->assessorUser1 = User::factory()->create();
        $this->assessorUser1->attachRole(RoleEnum::Assessor, $this->subsidy->id);
        $this->assessorUser2 = User::factory()->create();
        $this->assessorUser2->attachRole(RoleEnum::Assessor, $this->subsidy->id);

        $this->application1 = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
            ]
        );
        $this->application1Stage1 = ApplicationStage::factory()
            ->for($this->subsidyStage1)
            ->create(
                [
                'application_id' => $this->application1->id,
                'sequence_number' => 1,
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
        $this->application2Stage1 = ApplicationStage::factory()
            ->for($this->subsidyStage1)
            ->create(
                [
                'application_id' => $this->application2->id,
                'sequence_number' => 1,
                ]
            );
        $this->application2Stage2 = ApplicationStage::factory()
            ->for($this->subsidyStage2)
            ->create(
                [
                'application_id' => $this->application2->id,
                'assessor_user_id' => $this->assessorUser1,
                'sequence_number' => 2,
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
        $this->application3Stage1 = ApplicationStage::factory()
            ->for($this->subsidyStage1)
            ->create(
                [
                'application_id' => $this->application3->id,
                ]
            );

        $this->application3Stage2 = ApplicationStage::factory()
            ->for($this->subsidyStage2)
            ->create(
                [
                'application_id' => $this->application3->id,
                'assessor_user_id' => $this->assessorUser2,
                'sequence_number' => 2,
                ]
            );

        $this->application4 = Application::factory()
            ->for($this->subsidyVersion)
            ->create(
                [
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                ]
            );
        $this->application4Stage1 = ApplicationStage::factory()
            ->for($this->subsidyStage1)
            ->for($this->application4)
            ->create([
                'is_current' => false,
                'is_submitted' => true,
            ]);

        $this->application4Stage2 = ApplicationStage::factory()
            ->for($this->subsidyStage2)
            ->for($this->application4)
            ->create([
                'sequence_number' => 2,
            ]);
    }

    public function testFilterForImplementationCoordinator(): void
    {
        $filters = $this->getFiltersForApplication($this->application1);

        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus(200);

        $this->assertJsonFragment($response, $this->application1, ['show']);
    }

    public function testFilterForAssessorOnClaimedApplication(): void
    {
        $filters = $this->getFiltersForApplication($this->application2);

        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus(200);

        $this->assertJsonFragment($response, $this->application2, ['release', 'show']);
    }

    /**
     * @group application-open
     */
    public function testFilterForAssessorOnOpenApplication(): void
    {
        $filters = $this->getFiltersForApplication($this->application4);

        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus(200);

        $this->assertJsonFragment($response, $this->application4, ['claim']);
    }

    public function testListAsAssessor(): void
    {
        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'application_title' => $this->application1->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application1->status->value,
            'final_review_deadline' => $this->application1->final_review_deadline,
            'updated_at' => $this->application1->updated_at,
            'actions' => [],
        ]);
        $response->assertJsonFragment([
            'application_title' => $this->application2->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application2->status->value,
            'final_review_deadline' => $this->application2->final_review_deadline,
            'updated_at' => $this->application2->updated_at,
            'actions' => ['release', 'show'],
        ]);
        $response->assertJsonFragment([
            'application_title' => $this->application4->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application4->status->value,
            'final_review_deadline' => $this->application4->final_review_deadline,
            'updated_at' => $this->application4->updated_at,
            'actions' => ['claim'],
        ]);
    }

    public function testListAsImplementationCoordinator(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::ImplementationCoordinator, $this->subsidy->id);

        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'application_title' => $this->application1->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $this->application1->status->value,
            'final_review_deadline' => $this->application1->final_review_deadline,
            'updated_at' => $this->application1->updated_at,
            'actions' => ['show'],
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

    public function testMyListAsAssessor(): void
    {
        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications/assigned');

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
        yield 'reference' => [
            [
                'reference' => "123test123",
            ],
            200,
            '{"data":[]}',
        ];
        yield 'dateFrom' => [
            [
                'date_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[]}',
        ];
        yield 'dateTo' => [
            [
                'date_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[]}',
        ];
        yield 'dateLastModifiedFrom' => [
            [
                'date_last_modified_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[]}',
        ];
        yield 'dateLastModifiedTo' => [
            [
                'date_last_modified_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[]}',
        ];
        yield 'dateFinalReviewDeadLineFrom' => [
            [
                'date_final_review_deadline_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[]}',
        ];
        yield 'dateFinalReviewDeadLineTo' => [
            [
                'date_final_review_deadline_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[]}',
        ];
        yield 'Status' => [
            [
                'status' => ['test'],
            ],
            422,
            '{"message":"Gekozen status.0 is ongeldig.","errors":{"status.0":["Gekozen status.0 is ongeldig."]}}', // @phpcs:ignore
        ];
        yield 'Subsidy' => [
            [
                'subsidy' => ['test'],
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
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus($status);
        $response->assertContent($content);
    }

    private function getFiltersForApplication(Application $application): array
    {
        return [
            'reference' => $application->reference,
            'date_from' => $application->created_at,
            'date_to' => $application->created_at,
            'date_last_modified_from' => $application->updated_at,
            'date_last_modified_to' => $application->updated_at,
            'date_final_review_deadline_from' => $application->final_review_deadline,
            'date_final_review_deadline_to' => $application->final_review_deadline,
            'status' => [$application->status->value],
            'subsidy' => [$this->subsidy->code],
        ];
    }

    private function assertJsonFragment(TestResponse $response, Application $application, array $actions): void
    {
        $response->assertJsonFragment([
            'reference' => $application->reference,
            'subsidy' => $this->subsidy->code,
            'status' => $application->status->value,
            'final_review_deadline' => $application->final_review_deadline,
            'updated_at' => $application->updated_at,
            'actions' => $actions
        ]);
    }
}
