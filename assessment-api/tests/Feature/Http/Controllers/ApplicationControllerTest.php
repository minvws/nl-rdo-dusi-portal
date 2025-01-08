<?php

declare(strict_types=1);

namespace Feature\Http\Controllers;

use Carbon\Carbon;
use Generator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
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
    use WithoutMiddleware;

    private Application $application1;
    private ApplicationStage $application1Stage1;
    private ApplicationStage $application1Stage2;

    private Application $application2;
    private ApplicationStage $application2Stage1;
    private ApplicationStage $application2Stage2;

    private Application $application3;
    private ApplicationStage $application3Stage1;
    private ApplicationStage $application3Stage2;

    private Application $application4;
    private ApplicationStage $application4Stage1;
    private ApplicationStage $application4Stage2;

    private Application $application5;
    private ApplicationStage $application5Stage1;

    private Application $application6;
    private ApplicationStage $application6Stage1;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStage $subsidyStage2;
    private SubsidyStage $subsidyStage3;

    private Authenticatable $implementationCoordinatorUser;
    private Authenticatable $assessorUser1;
    private Authenticatable $assessorUser2;


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
                'application_title' => 'application 1',
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Pending,
            ]
        );

        // There must be a submitted stage, to load the submitted_at date.
        $this->application1Stage1 = ApplicationStage::factory()
            ->for($this->application1)
            ->for($this->subsidyStage1)
            ->submitted(Carbon::now())
            ->create([
                'sequence_number' => 1,
            ]);

        $this->application1Stage2 = ApplicationStage::factory()
            ->for($this->application1)
            ->for($this->subsidyStage2)
            ->create([
                'sequence_number' => 2,
            ]);

        $this->application2 = Application::factory()->create(
            [
                'application_title' => 'application 2',
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::RequestForChanges,
            ]
        );
        $this->application2Stage1 = ApplicationStage::factory()
            ->for($this->application2)
            ->for($this->subsidyStage1)
            ->submitted(Carbon::now())
            ->create([
                'sequence_number' => 1,
            ]);
        $this->application2Stage2 = ApplicationStage::factory()
            ->for($this->application2)
            ->for($this->subsidyStage2)
            ->for($this->assessorUser1, 'assessorUser')
            ->create([
                'sequence_number' => 2,
            ]);

        $this->application3 = Application::factory()->create(
            [
                'application_title' => 'application 3',
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Pending,
            ]
        );
        $this->application3Stage1 = ApplicationStage::factory()
            ->for($this->application3)
            ->for($this->subsidyStage1)
            ->for($this->assessorUser2, 'assessorUser')
            ->submitted(Carbon::now())
            ->create();

        $this->application3Stage2 = ApplicationStage::factory()
            ->for($this->application3)
            ->for($this->subsidyStage2)
            ->for($this->assessorUser2, 'assessorUser')
            ->create([
                'sequence_number' => 2,
            ]);

        $this->application4 = Application::factory()
            ->for($this->subsidyVersion)
            ->create(
                [
                    'application_title' => 'application 4',
                    'updated_at' => Carbon::today(),
                    'created_at' => Carbon::today(),
                    'final_review_deadline' => Carbon::today(),
                    'status' => ApplicationStatus::Pending,
                ]
            );
        $this->application4Stage1 = ApplicationStage::factory()
            ->for($this->subsidyStage1)
            ->for($this->application4)
            ->submitted(Carbon::now())
            ->create();

        $this->application4Stage2 = ApplicationStage::factory()
            ->for($this->subsidyStage2)
            ->for($this->application4)
            ->create([
                'sequence_number' => 2,
            ]);

        $this->application5 = Application::factory()->create(
            [
                'application_title' => 'application 5',
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Draft,
            ]
        );
        $this->application5Stage1 = ApplicationStage::factory()
            ->for($this->application5)
            ->for($this->subsidyStage1)
            ->create([
                'sequence_number' => 1,
            ]);

        $this->application6 = Application::factory()->create(
            [
                'application_title' => 'application 6',
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Approved,
            ]
        );
        $this->application6Stage1 = ApplicationStage::factory()
            ->for($this->subsidyStage1)
            ->for($this->application6)
            ->create();
    }

    public function testFilterForImplementationCoordinator(): void
    {
        $filters = $this->getFiltersForApplication($this->application1);

        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertJsonFragment($response, $this->application1, ['assign', 'show']);
    }

    public function testFilterForAssessorOnClaimedApplication(): void
    {
        $filters = $this->getFiltersForApplication($this->application2);

        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications', $filters);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

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
        $response->assertJsonCount(1, 'data');

        $this->assertJsonFragment($response, $this->application4, ['claim']);
    }

    /**
     * @group list-all-applications
     */
    public function testListAllApplicationsAsAssessor(): void
    {
        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);

        $response->assertJsonCount(3, 'data');


        $this->assertJsonFragment($response, $this->application1, ['release', 'show']);
        $this->assertJsonFragment($response, $this->application2, ['release', 'show']);
        $this->assertJsonFragment($response, $this->application4, ['claim']);

        // Don't show the one where you are not the assessor
        $response->assertJsonMissing([
         'application_title' => $this->application3->application_title,
        ]);

        // Don't show draft
        $response->assertJsonMissing([
         'application_title' => $this->application5->application_title,
        ]);

        // Don't show approved
        $response->assertJsonMissing([
         'application_title' => $this->application6->application_title,
        ]);
    }

    public function testListAllApplicationsAsImplementationCoordinator(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::ImplementationCoordinator, $this->subsidy->id);

        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);
        $response->assertJsonCount(4, 'data');

        $this->assertJsonFragment($response, $this->application1, ['assign', 'show']);
        $this->assertJsonFragment($response, $this->application2, ['release', 'show']);

        // Don't show draft
        $response->assertJsonMissing([
            'application_title' => $this->application5->application_title,
        ]);
    }

    /**
     * @group dont-ListAllApplications-unsubmitted
     */
    public function testListAllApplicationsAsAnyUserShouldNotListUnsubmittedApplications(): void
    {
        $user = User::factory()->create();

        $values = RoleEnum::cases();
        $randomRole = $values[array_rand($values)];
        $user->attachRole($randomRole, $this->subsidy->id);

        $unsubmittedApplication = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Draft->value,
            ]
        );

        ApplicationStage::factory()
            ->for($unsubmittedApplication)
            ->create(
                [
                    'sequence_number' => 1,
                ]
            );


        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);
        $response->assertJsonCount(4, 'data');

        $response->assertJsonMissing([
            'application_title' => $unsubmittedApplication->application_title,
        ]);

        $response->assertJsonMissing([
            'application_title' => $this->application5->application_title,
        ]);
    }

    public function testMyListAsAssessor(): void
    {
        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications/assigned');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertJsonFragment($response, $this->application2, ['release', 'show']);

        // Don't show the one where you are not the assessor
        $response->assertJsonMissing([
            'application_title' => $this->application3->application_title,
        ]);
        $response->assertJsonMissing([
            'application_title' => $this->application5->application_title,
        ]);
    }

    /**
     * @group list-handled
     */
    public function testMyListAsAssessorShouldShowHandledApplications(): void
    {
        //Create application which was handled previously by assessor but is currently handled by someone else.
        $application = Application::factory()->create(
            [
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Pending
            ]
        );

        ApplicationStage::factory()
            ->for($this->subsidyStage1)
            ->for($application)
            ->create([
                'sequence_number' => 1,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => Carbon::today(),
            ]);

        ApplicationStage::factory()
            ->for($this->subsidyStage2)
            ->for($application)
            ->for($this->assessorUser2, 'assessorUser')
            ->create([
                'sequence_number' => 2,
                'is_current' => false,
                'is_submitted' => true,
                'submitted_at' => Carbon::today(),
            ]);

        ApplicationStage::factory()
            ->for($this->subsidyStage3)
            ->for($application)
            ->for($this->assessorUser1, 'assessorUser')
            ->create([
                'sequence_number' => 3,
                'is_current' => true,
            ]);

        $response = $this
            ->be($this->assessorUser2)
            ->json('GET', '/api/applications/assigned');

        $response->assertStatus(200);

        //Handled application should be present
        $response->assertJsonFragment([
            'application_title' => $application->application_title,
            'subsidy' => $this->subsidy->code,
            'status' => $application->status->value,
            'final_review_deadline' => $application->final_review_deadline->format('Y-m-d'),
            'updated_at' => $application->updated_at,
            'actions' => ['show'],
        ]);
    }

    public static function noResultFilterProvider(): Generator
    {
        yield 'reference' => [
            [
                'reference' => "123test123",
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
        ];
        yield 'dateFrom' => [
            [
                'date_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
        ];
        yield 'dateTo' => [
            [
                'date_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
        ];
        yield 'dateLastModifiedFrom' => [
            [
                'date_last_modified_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
        ];
        yield 'dateLastModifiedTo' => [
            [
                'date_last_modified_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
        ];
        yield 'dateFinalReviewDeadLineFrom' => [
            [
                'date_final_review_deadline_from' => Carbon::tomorrow(),
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
        ];
        yield 'dateFinalReviewDeadLineTo' => [
            [
                'date_final_review_deadline_to' => Carbon::yesterday(),
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
        ];
        yield 'Status' => [
            [
                'status' => ['test'],
            ],
            422,
            '{"message":"Gekozen status.0 is ongeldig.","errors":{"status.0":["Gekozen status.0 is ongeldig."]}}',
        ];
        yield 'Subsidy' => [
            [
                'subsidy' => ['test'],
            ],
            200,
            '{"data":[],"links":{"first":"http:\/\/localhost\/api\/applications?page=1","last":"http:\/\/localhost\/api\/applications?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":null,"last_page":1,"links":[{"url":null,"label":"&laquo; Vorige","active":false},{"url":"http:\/\/localhost\/api\/applications?page=1","label":"1","active":true},{"url":null,"label":"Volgende &raquo;","active":false}],"path":"http:\/\/localhost\/api\/applications","per_page":15,"to":null,"total":0,"sorts":[{"column":"final_review_deadline","direction":"ascending"},{"column":"updated_at","direction":"ascending"}]}}', // phpcs:ignore
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
            'date_from' => $application->submitted_at,
            'date_to' => $application->submitted_at,
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
            'final_review_deadline' => $application->final_review_deadline?->format('Y-m-d'),
            'updated_at' => $application->updated_at,
            'actions' => $actions
        ]);
    }

    public function testAsLegalSpecialistListApplications(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::LegalSpecialist, $this->subsidy->id);

        $application = Application::factory()->create(
            [
                'application_title' => 'application rejected',
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Rejected,
            ]
        );
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStage1)
            ->create();

        $response = $this
            ->be($user)
            ->json('GET', '/api/applications');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function testAsLegalSpecialistListApplicationByReference(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::LegalSpecialist, $this->subsidy->id);

        $application = Application::factory()->create(
            [
                'application_title' => 'application rejected',
                'subsidy_version_id' => $this->subsidyVersion->id,
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::today(),
                'status' => ApplicationStatus::Rejected,
            ]
        );
        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStage1)
            ->create();

        $response = $this
            ->be($user)
            ->json('GET', '/api/applications?reference=' . $application->reference);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $this->assertJsonFragment($response, $application, ['show']);
    }

    public function testAsLegalSpecialistListApplicationByReferenceGivesNoResultWhenApplicationInNotFinished(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::LegalSpecialist, $this->subsidy->id);

        $response = $this
            ->be($user)
            ->json('GET', '/api/applications?reference=' . $this->application1->reference);

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @dataProvider sortProvider
     */
    public function testSortMetaDataIsReturned(string $sortParameter, array $expectedSortsResponse): void
    {
        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications', ['sort' => $sortParameter]);

        $response->assertJsonPath('meta.sorts', $expectedSortsResponse);
    }

    public static function sortProvider(): Generator
    {
        yield 'sort by final_review_deadline' => [
            'final_review_deadline',
            [
                [
                    'column' => 'final_review_deadline',
                    'direction' => 'ascending',
                ],
                [
                    'column' => 'updated_at',
                    'direction' => 'ascending',
                ],
            ]
        ];
        yield 'sort by -final_review_deadline' => [
            '-final_review_deadline',
            [
                [
                    'column' => 'final_review_deadline',
                    'direction' => 'descending',
                ],
                [
                    'column' => 'updated_at',
                    'direction' => 'ascending',
                ],
            ]
        ];
        yield 'sort by updated_at' => [
            'updated_at',
            [
                [
                    'column' => 'updated_at',
                    'direction' => 'ascending',
                ],
            ]
        ];
        yield 'sort by -updated_at' => [
            '-updated_at',
            [
                [
                    'column' => 'updated_at',
                    'direction' => 'descending',
                ],
            ]
        ];
    }

    /**
     * @param array $queryParameters
     * @param int $expectedPerPage
     * @param int $expectedPage
     * @return void
     * @dataProvider paginationProvider
     */
    public function testPagination(array $queryParameters, int $expectedPerPage, int $expectedPage): void
    {
        $response = $this
            ->be($this->assessorUser1)
            ->json('GET', '/api/applications', $queryParameters);

        $this->assertSame($response->json('meta.per_page'), $expectedPerPage);
        $this->assertSame($response->json('meta.current_page'), $expectedPage);
    }

    public static function paginationProvider(): Generator
    {
        yield 'without pagination parameters' => [
            [],
            15,
            1,
        ];
        yield '30 per page' => [
            [
                'per_page' => 30,
            ],
            30,
            1,
        ];
        yield '30 per page and 2nd page' => [
            [
                'per_page' => 30,
                'page' => 2,
            ],
            30,
            2,
        ];
        yield '2nd page' => [
            [
                'page' => 2,
            ],
            15,
            2,
        ];
    }
}
