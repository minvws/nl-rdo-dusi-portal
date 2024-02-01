<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use MinVWS\DUSi\Shared\Application\Events\ApplicationMessageEvent;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Test\MocksEncryption;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use Ramsey\Uuid\Uuid;

/**
 * @group application-flow
 */
class ApplicationFlowServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use MocksEncryption;

    private const REVIEW_PERIOD = 7; // days

    private const VALUE_APPROVED = ApplicationStatus::Approved->value;
    private const VALUE_REJECTED = ApplicationStatus::Rejected->value;
    private const VALUE_REQ_CHANGES = ApplicationStatus::RequestForChanges->value;
    private const VALUE_AGREES = 'agrees';
    private const VALUE_DISAGREES = 'disagrees';

    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private Field $subsidyStage1Field;
    private Field $subsidyStage1Upload;
    private SubsidyStage $subsidyStage2;
    private Field $subsidyStage2Field;
    private SubsidyStage $subsidyStage3;
    private Field $subsidyStage3Field;
    private SubsidyStageTransition $subsidyStageTransition3To2;

    private Application $application;
    private ApplicationStage $applicationStage1;
    private string $applicationStage1UploadId;

    private ApplicationStageEncryptionService $encryptionService;
    private ApplicationFlowService $flowService;
    private ApplicationFileManager $fileRepository;

    private CarbonImmutable $now;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(Disk::APPLICATION_FILES);
        Event::fake();

        $this->encryptionService = $this->app->get(ApplicationStageEncryptionService::class);
        $this->flowService = $this->app->get(ApplicationFlowService::class);
        $this->fileRepository = $this->app->get(ApplicationFileManager::class);

        $subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()->for($subsidy)->create([
            'review_period' => self::REVIEW_PERIOD
        ]);
        $this->subsidyStage1 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 1,
            'subject_role' => SubjectRole::Applicant
        ]);
        $this->subsidyStage1Field =
            Field::factory()->for($this->subsidyStage1)->create(['type' => FieldType::CustomBankAccount]);
        $this->subsidyStage1Upload = Field::factory()->for($this->subsidyStage1)->create(['type' => FieldType::Upload]);
        $this->subsidyStage2 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 2,
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::Assessor,
        ]);
        $this->subsidyStage2Field = Field::factory()->for($this->subsidyStage1)->create(['type' => FieldType::Text]);
        $this->subsidyStage3 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 3,
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::InternalAuditor,
        ]);
        $this->subsidyStage3Field = Field::factory()->for($this->subsidyStage1)->create(['type' => FieldType::Text]);

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage1, 'currentSubsidyStage')
            ->for($this->subsidyStage2, 'targetSubsidyStage')
            ->create([
                'target_application_status' => ApplicationStatus::Submitted,
                'condition' => null
            ]);

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage2, 'currentSubsidyStage')
            ->for($this->subsidyStage3, 'targetSubsidyStage')
            ->create([
                'condition' => null
            ]);

        $transition = SubsidyStageTransition::factory()
            ->for($this->subsidyStage3, 'currentSubsidyStage')
            ->for($this->subsidyStage1, 'targetSubsidyStage')
            ->create([
                'target_application_status' => ApplicationStatus::RequestForChanges,
                'clone_data' => true,
                'send_message' => true,
                'condition' => new AndCondition([
                    new ComparisonCondition(
                        2,
                        $this->subsidyStage2Field->code,
                        Operator::Identical,
                        self::VALUE_REQ_CHANGES,
                    ),
                    new ComparisonCondition(
                        3,
                        $this->subsidyStage3Field->code,
                        Operator::Identical,
                        self::VALUE_AGREES
                    )
                ])
            ]);
        SubsidyStageTransitionMessage::factory()->for($transition)->create();

        $this->subsidyStageTransition3To2 = SubsidyStageTransition::factory()
            ->for($this->subsidyStage3, 'currentSubsidyStage')
            ->for($this->subsidyStage2, 'targetSubsidyStage')
            ->create([
                'condition' => new AndCondition([
                    new InCondition(
                        2,
                        $this->subsidyStage2Field->code,
                        [self::VALUE_APPROVED, self::VALUE_REJECTED, self::VALUE_REQ_CHANGES],
                    ),
                    new ComparisonCondition(
                        3,
                        $this->subsidyStage3Field->code,
                        Operator::Identical,
                        self::VALUE_DISAGREES
                    )
                ]),
                'assign_to_previous_assessor' => true
            ]);

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage3, 'currentSubsidyStage')
            ->create([
                'target_application_status' => ApplicationStatus::Approved,
                'condition' => new AndCondition([
                    new ComparisonCondition(
                        2,
                        $this->subsidyStage2Field->code,
                        Operator::Identical,
                        self::VALUE_APPROVED,
                    ),
                    new ComparisonCondition(
                        3,
                        $this->subsidyStage3Field->code,
                        Operator::Identical,
                        self::VALUE_AGREES
                    )
                ])
            ]);

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage3, 'currentSubsidyStage')
            ->create([
                'target_application_status' => ApplicationStatus::Rejected,
                'condition' => new AndCondition([
                    new ComparisonCondition(
                        2,
                        $this->subsidyStage2Field->code,
                        Operator::Identical,
                        self::VALUE_REJECTED,
                    ),
                    new ComparisonCondition(
                        3,
                        $this->subsidyStage3Field->code,
                        Operator::Identical,
                        self::VALUE_AGREES
                    )
                ])
            ]);

        $this->application = Application::factory()->for($this->subsidyVersion)->create();

        $this->applicationStage1 =
            ApplicationStage::factory()
                ->for($this->application)
                ->for($this->subsidyStage1)
                ->create();

        $this->createAnswer($this->applicationStage1, $this->subsidyStage1Field, $this->faker->word);
        $this->applicationStage1UploadId = Uuid::uuid4()->toString();
        $this->fileRepository->writeFile(
            $this->applicationStage1,
            $this->subsidyStage1Upload,
            $this->applicationStage1UploadId,
            $this->faker->paragraph(5)
        );
        $fileJson = json_encode([['id' => $this->applicationStage1UploadId]]);
        $this->assertIsString($fileJson);
        $this->createAnswer($this->applicationStage1, $this->subsidyStage1Upload, $fileJson);

        // make sure the factories don't do weird things with the initial data
        $this->assertTrue($this->applicationStage1->is_current);
        $this->assertFalse($this->applicationStage1->is_submitted);
        $this->assertNull($this->applicationStage1->submitted_at);
        $this->assertEquals(ApplicationStatus::Draft, $this->application->status);
        $this->assertNull($this->application->final_review_deadline);

        $this->now = CarbonImmutable::now()->setHour(10)->roundSecond();
        Carbon::setTestNow($this->now);
    }

    private function createAnswer(ApplicationStage $applicationStage, Field $field, string $value): Answer
    {
        $encrypter = $this->encryptionService->getEncrypter($applicationStage);
        return Answer::factory()
            ->for($applicationStage)
            ->for($field)
            ->create([
                'encrypted_answer' => $encrypter->encrypt($value)
            ]);
    }

    private function updateAnswer(ApplicationStage $applicationStage, Field $field, string $value): Answer
    {
        Answer::query()
            ->where('application_stage_id', '=', $applicationStage->id)
            ->where('field_id', '=', $field->id)
            ->delete();
        return $this->createAnswer($applicationStage, $field, $value);
    }

    public function testStage1Submit(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);

        $this->application->refresh();

        $this->assertFalse($this->applicationStage1->is_current);
        $this->assertTrue($this->applicationStage1->is_submitted);
        $this->assertEquals($this->now, $this->applicationStage1->submitted_at);

        $this->assertEquals(ApplicationStatus::Submitted, $this->application->status);
        $this->assertNotNull($this->application->final_review_deadline);
        $this->assertEquals(
            $this->now->addDays(self::REVIEW_PERIOD)->endOfDay()->floorSecond(),
            $this->application->final_review_deadline
        );

        $this->assertNotNull($stage2);
        $this->assertTrue($stage2->is_current);
        $this->assertFalse($stage2->is_submitted);
        $this->assertNull($stage2->submitted_at);
        $this->assertEquals($this->subsidyStage2->id, $stage2->subsidy_stage_id);
    }

    public function testStage2Submit(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $this->application->refresh();

        $applicationStatus = $this->application->status;
        $applicationFinalReviewDeadline = $this->application->final_review_deadline;

        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_APPROVED);

        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->application->refresh();

        $this->assertFalse($stage2->is_current);
        $this->assertTrue($stage2->is_submitted);
        $this->assertEquals($this->now, $stage2->submitted_at);

        // should not have changed
        $this->assertEquals($applicationStatus, $this->application->status);
        $this->assertEquals($applicationFinalReviewDeadline, $this->application->final_review_deadline);

        $this->assertNotNull($stage3);
        $this->assertTrue($stage3->is_current);
        $this->assertFalse($stage3->is_submitted);
        $this->assertNull($stage3->submitted_at);
        $this->assertEquals($this->subsidyStage3->id, $stage3->subsidy_stage_id);
    }

    public function testStage3SubmitAgreesWithRequestForChanges(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_REQ_CHANGES);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_AGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);

        $this->application->refresh();

        $this->assertFalse($stage3->is_current);
        $this->assertTrue($stage3->is_submitted);
        $this->assertEquals($this->now, $stage3->submitted_at);

        $this->assertEquals(ApplicationStatus::RequestForChanges, $this->application->status);
        $this->assertEquals(null, $this->application->final_review_deadline);

        $this->assertNotNull($nextStage);
        $this->assertEquals($this->subsidyStage1->id, $nextStage->subsidy_stage_id);
        $this->assertTrue($nextStage->is_current);
        $this->assertFalse($nextStage->is_submitted);
        $this->assertNull($nextStage->submitted_at);
        $this->assertCount(2, $nextStage->answers);
        $this->assertEquals($this->applicationStage1->encrypted_key, $nextStage->encrypted_key);
        $this->assertEquals(
            $this->applicationStage1->answers()->first()->encrypted_answer,
            $nextStage->answers()->first()->encrypted_answer
        );
        $this->assertTrue(
            $this->fileRepository->fileExists(
                $nextStage,
                $this->subsidyStage1Upload,
                $this->applicationStage1UploadId
            )
        );

        Event::assertDispatched(ApplicationMessageEvent::class);
    }

    public static function stage3SubmitDisagreesProvider(): array
    {
        return [
            'disagrees-with-approval' => [self::VALUE_APPROVED],
            'disagrees-with-rejection' => [self::VALUE_REJECTED],
            'disagrees-with-request-for-changes' => [self::VALUE_REQ_CHANGES],
        ];
    }

    /**
     * @dataProvider stage3SubmitDisagreesProvider
     */
    public function testStage3SubmitDisagrees(string $value): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $this->application->refresh();

        $applicationStatus = $this->application->status;
        $applicationFinalReviewDeadline = $this->application->final_review_deadline;

        $this->createAnswer($stage2, $this->subsidyStage2Field, $value);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_DISAGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);

        $this->application->refresh();

        $this->assertFalse($stage3->is_current);
        $this->assertTrue($stage3->is_submitted);
        $this->assertEquals($this->now, $stage3->submitted_at);

        $this->assertEquals($applicationStatus, $this->application->status);
        $this->assertEquals($applicationFinalReviewDeadline, $this->application->final_review_deadline);

        $this->assertNotNull($nextStage);
        $this->assertEquals($this->subsidyStage2->id, $nextStage->subsidy_stage_id);
        $this->assertTrue($nextStage->is_current);
        $this->assertFalse($nextStage->is_submitted);
        $this->assertNull($nextStage->submitted_at);
        $this->assertCount(0, $nextStage->answers);
    }

    public static function stage3SubmitAgreesProvider(): array
    {
        return [
            'agrees-with-approval' => [self::VALUE_APPROVED, ApplicationStatus::Approved],
            'agrees-with-rejection' => [self::VALUE_REJECTED, ApplicationStatus::Rejected],
        ];
    }

    /**
     * @dataProvider stage3SubmitAgreesProvider
     */
    public function testStage3SubmitAgrees(string $value, ApplicationStatus $expectedStatus): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $this->application->refresh();

        $this->createAnswer($stage2, $this->subsidyStage2Field, $value);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_AGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);

        $this->application->refresh();

        $this->assertFalse($stage3->is_current);
        $this->assertTrue($stage3->is_submitted);
        $this->assertEquals($this->now, $stage3->submitted_at);

        $this->assertEquals($expectedStatus, $this->application->status);
        $this->assertNull($nextStage);
    }

    public function testRequestForChangesMovesFinalReviewDeadline(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $this->application->refresh();

        $initialDeadline = CarbonImmutable::instance($this->application->final_review_deadline);

        $now = $this->now->addDays(2);
        Carbon::setTestNow($now);
        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_REQ_CHANGES);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->application->refresh();

        $this->assertEquals($initialDeadline, $this->application->final_review_deadline);

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_AGREES);
        $newStage1 = $this->flowService->submitApplicationStage($stage3);

        $this->application->refresh();

        $now = $now->addDays(3);  // applicant took 3 days
        Carbon::setTestNow($now);
        $this->flowService->submitApplicationStage($newStage1);

        $this->application->refresh();

        $this->assertEquals(
            3,
            Carbon::instance($initialDeadline)
                ->diffAsCarbonInterval($this->application->final_review_deadline)
                ->totalDays
        );
    }

    public function testStaticFinalReviewDeadline(): void
    {
        $this->subsidyVersion->review_deadline = $this->now->addDays(14)->endOfDay()->floorSecond();
        $this->subsidyVersion->review_period = null;
        $this->subsidyVersion->save();

        $this->application->refresh();
        $this->applicationStage1->refresh();

        $this->flowService->submitApplicationStage($this->applicationStage1);

        $this->application->refresh();

        $this->assertEquals($this->subsidyVersion->review_deadline, $this->application->final_review_deadline);
    }

    public function testNoTransitionFound(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        // value for which no transition matches
        $this->createAnswer($stage2, $this->subsidyStage2Field, $this->faker->word);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->expectException(ApplicationFlowException::class);
        $this->expectExceptionMessage('No matching transition found for submit!');

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_AGREES);
        $this->flowService->submitApplicationStage($stage3);
    }

    public function testAssignToPreviousAssessor(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $assessor = User::factory()->create();
        $assessor->attachRole(Role::Assessor, $this->subsidyVersion->subsidy_id);
        $stage2->assessor_user_id = $assessor->id;
        $stage2->save();

        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_APPROVED);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_DISAGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);
        $this->assertEquals($assessor->id, $nextStage->assessor_user_id);
    }

    public function testAssignToPreviousAssessorTurnedOff(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $assessor = User::factory()->create();
        $assessor->attachRole(Role::Assessor, $this->subsidyVersion->subsidy_id);
        $stage2->assessor_user_id = $assessor->id;
        $stage2->save();

        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_APPROVED);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $this->subsidyStageTransition3To2->assign_to_previous_assessor = false;
        $this->subsidyStageTransition3To2->save();

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_DISAGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);
        $this->assertNull($nextStage->assessor_user_id);
    }

    public function testAssignToPreviousAssessorShouldNotAssignIfUserNotActiveAnymore(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $assessor = User::factory()->create();
        $assessor->attachRole(Role::Assessor, $this->subsidyVersion->subsidy_id);
        $stage2->assessor_user_id = $assessor->id;
        $stage2->save();

        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_APPROVED);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $assessor->active_until = CarbonImmutable::yesterday();
        $assessor->save();

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_DISAGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);
        $this->assertNull($nextStage->assessor_user_id);
    }

    public function testAssignToPreviousAssessorShouldNotAssignIfUserDoesNotHaveRoleAnymore(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $assessor = User::factory()->create();
        $assessor->attachRole(Role::Assessor, $this->subsidyVersion->subsidy_id);
        $stage2->assessor_user_id = $assessor->id;
        $stage2->save();

        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_APPROVED);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        $assessor->detachRole(Role::Assessor, $this->subsidyVersion->subsidy_id);
        $assessor->save();

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_DISAGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);
        $this->assertNull($nextStage->assessor_user_id);
    }


    public function testAssignToPreviousAssessorShouldNotAssignIfUserPickedUpEarlierStage(): void
    {
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->assertNotNull($stage2);

        $assessor = User::factory()->create();
        $assessor->attachRole(Role::Assessor, $this->subsidyVersion->subsidy_id);
        $stage2->assessor_user_id = $assessor->id;
        $stage2->save();

        $this->createAnswer($stage2, $this->subsidyStage2Field, self::VALUE_APPROVED);
        $stage3 = $this->flowService->submitApplicationStage($stage2);

        // NOTE: although this is the applicant stage this doesn't really matter for the check we are testing
        $this->applicationStage1->assessor_user_id = $assessor->id;
        $this->applicationStage1->save();

        $this->createAnswer($stage3, $this->subsidyStage3Field, self::VALUE_DISAGREES);
        $nextStage = $this->flowService->submitApplicationStage($stage3);
        $this->assertNull($nextStage->assessor_user_id);
    }

    public function testCreationOfApplicationStageTransition(): void
    {
        $this->assertEquals(0, $this->application->applicationStageTransitions->count());
        $stage2 = $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->application->refresh();
        $this->assertEquals(1, $this->application->applicationStageTransitions->count());
        $transition = $this->application->applicationStageTransitions[0];
        $this->assertInstanceOf(ApplicationStageTransition::class, $transition);
        $this->assertEquals($this->applicationStage1->id, $transition->previousApplicationStage->id);
        $this->assertEquals(ApplicationStatus::Draft, $transition->previous_application_status);
        $this->assertEquals($stage2->id, $transition->newApplicationStage->id);
        $this->assertEquals(ApplicationStatus::Submitted, $transition->new_application_status);
    }

    public function testUpdatedAtOfApplicationIsUpdatedAfterApplicationStageSubmit(): void
    {
        $now = CarbonImmutable::now()->startOfDay();
        Date::setTestNow($now);

        // Set start updated_at
        $this->application->update([
            'updated_at' => $now
        ]);

        // Submit after an hour
        $nowWithHour = $now->addHour();
        Date::setTestNow($nowWithHour);

        $this->flowService->submitApplicationStage($this->applicationStage1);
        $this->application->refresh();

        // Updated at should be updated
        $this->assertFalse($now->eq($this->application->updated_at));
        $this->assertTrue($nowWithHour->eq($this->application->updated_at));
    }
}
