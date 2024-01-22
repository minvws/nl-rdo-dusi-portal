<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\MockedBankAccountRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\SurePayService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FieldValidationParams;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Test\ApplicationCreator;
use MinVWS\DUSi\Shared\Test\AssessmentOutcome;
use MinVWS\DUSi\Shared\Test\ComplexSubsidyAggregateManager;
use MinVWS\DUSi\Shared\Test\MocksEncryption;
use Illuminate\Foundation\Testing\WithFaker;

class ApplicationControllerWithFlowTest extends TestCase
{
    use MocksEncryption;
    use WithFaker;

    private ApplicationFlowService $flowService;
    private ApplicationRepository $applicationRepository;
    private ApplicationStageEncryptionService $encryptionService;

    private ComplexSubsidyAggregateManager $subsidyManager;

    private ApplicationCreator $applicationCreator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupMocksEncryption();

        $this->flowService = $this->app->get(ApplicationFlowService::class);
        $this->applicationRepository = $this->app->get(ApplicationRepository::class);
        $this->encryptionService = $this->app->get(ApplicationStageEncryptionService::class);

        $this->subsidyManager = $this->app->get(ComplexSubsidyAggregateManager::class);
        $this->subsidyManager->setup();
    }

    public function testCompleteComplexFlowSuccess(): void
    {

        $application = $this->subsidyManager->createApplication();
        $applicationStage1 = $this->subsidyManager->createApplicationStage($application, 1);

        [$assessor1, $assessor2, $internalAuditor, $implementationCoordinator] = $this->subsidyManager->getUsers();

        $applicationStage2 = $this->flowService->submitApplicationStage($applicationStage1);
        $this->applicationRepository->assignApplicationStage($applicationStage2, $assessor1);

        $firstAssessmentField = $this->subsidyManager->getSubsidyStageField('firstAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage2,
            $firstAssessmentField,
            AssessmentOutcome::APPROVED->value
        );
        $applicationStage3 = $this->flowService->submitApplicationStage($applicationStage2);

        $this->applicationRepository->assignApplicationStage($applicationStage3, $assessor2);

        $secondAssessmentField = $this->subsidyManager->getSubsidyStageField('secondAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage3,
            $secondAssessmentField,
            AssessmentOutcome::AGREES->value
        );

        $applicationStage4 = $this->flowService->submitApplicationStage($applicationStage3);
        $this->applicationRepository->assignApplicationStage($applicationStage3, $internalAuditor);

        $internalAssessmentField = $this->subsidyManager->getSubsidyStageField('internalAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage4,
            $internalAssessmentField,
            AssessmentOutcome::APPROVED->value
        );

        $implementationCoordinatorStage = $this->flowService->submitApplicationStage($applicationStage4);
        $this->applicationRepository->assignApplicationStage($applicationStage4, $implementationCoordinator);

        $implementationCoordinatorAssessmentField =
            $this->subsidyManager->getSubsidyStageField('implementationCoordinatorAssessment');
        $this->subsidyManager->createAnswer(
            $implementationCoordinatorStage,
            $implementationCoordinatorAssessmentField,
            AssessmentOutcome::APPROVED->value
        );
        $nextApplicationStage = $this->flowService->submitApplicationStage($implementationCoordinatorStage);

        $this->assertNull($nextApplicationStage);

        $application->refresh();
        $this->assertEquals(ApplicationStatus::Approved, $application->status);
    }

    /**
     * @group application-claim
     */
    public function testAssessorShouldBeAbleToClaimApplicationStageWhichHeHasAssessedBeforeInReturnFlow(): void
    {

        $application = $this->subsidyManager->createApplication();
        $applicationStage1 = $this->subsidyManager->createApplicationStage($application, 1);

        [$assessor1, $assessor2, $internalAuditor] = $this->subsidyManager->getUsers();

        $applicationStage2 = $this->flowService->submitApplicationStage($applicationStage1);
        $this->applicationRepository->assignApplicationStage($applicationStage2, $assessor1);

        $firstAssessmentField = $this->subsidyManager->getSubsidyStageField('firstAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage2,
            $firstAssessmentField,
            AssessmentOutcome::APPROVED->value
        );
        $applicationStage3 = $this->flowService->submitApplicationStage($applicationStage2);

        $this->applicationRepository->assignApplicationStage($applicationStage3, $assessor2);

        $secondAssessmentField = $this->subsidyManager->getSubsidyStageField('secondAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage3,
            $secondAssessmentField,
            AssessmentOutcome::AGREES->value
        );

        $applicationStage4 = $this->flowService->submitApplicationStage($applicationStage3);
        $this->applicationRepository->assignApplicationStage($applicationStage3, $internalAuditor);

        $internalAssessmentField = $this->subsidyManager->getSubsidyStageField('internalAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage4,
            $internalAssessmentField,
            AssessmentOutcome::REJECTED->value
        );

        $currentApplicationStage = $this->flowService->submitApplicationStage($applicationStage4);
        $this->assertEquals($currentApplicationStage->assessor_user_id, $assessor1->id);

        $this->subsidyManager->createAnswer(
            $currentApplicationStage,
            $firstAssessmentField,
            AssessmentOutcome::APPROVED->value
        );
        $nextApplicationStage = $this->flowService->submitApplicationStage($currentApplicationStage);

        $this->assertEquals(3, $nextApplicationStage->subsidyStage->stage);

        $response = $this
            ->be($assessor2)
            ->json('PUT', '/api/applications/' . $application->id . '/assessor')
        ;
        $response->assertStatus(200);
    }

    /**
     * @group surepay-result-encryption
     */
    public function testCheckSurepayResultEncryptionWhenInRequestChangesFlow(): void
    {
        $bankAccountField = Field::factory()
            ->for($this->subsidyManager->getSubsidyStage(1))
            ->create([
                         'code' => 'bankAccountNumber',
                         'type' => FieldType::CustomBankAccount,
                     ]);
        $bankAccountHolderField = Field::factory()
            ->for($this->subsidyManager->getSubsidyStage(1))
            ->create([
                         'code' => 'bankAccountHolder',
                         'type' => FieldType::Text,
                     ]);

        [$assessor1, $assessor2, $internalAuditor] = $this->subsidyManager->getUsers();

        $application = $this->subsidyManager->createApplication();
        $applicationStage1Sequence1 = $this->subsidyManager->createApplicationStage($application, 1);

        $body = new FieldValidationParams(
            (object) [
                $bankAccountField->code => MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_CLOSE_MATCH,
                $bankAccountHolderField->code =>  $this->faker->lastname,
            ]
        );

        $this->app->get(ApplicationDataService::class)->saveApplicationStageData(
            $applicationStage1Sequence1,
            $body->data,
            submit: true,
        );

        $applicationStage2Sequence2 = $this->flowService->submitApplicationStage($applicationStage1Sequence1);

        $surePayService = $this->app->get(SurePayService::class);
        $surePayService->checkSurePayForApplication($application);

        $application->refresh();

        $this->applicationRepository->assignApplicationStage($applicationStage2Sequence2, $assessor1);

        $firstAssessmentField = $this->subsidyManager->getSubsidyStageField('firstAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage2Sequence2,
            $firstAssessmentField,
            AssessmentOutcome::SUPPLEMENT_NEEDED->value
        );
        $applicationStage1Sequence3 = $this->flowService->submitApplicationStage($applicationStage2Sequence2);

        /** @var ApplicationDataService $applicationDataService */
        $applicationDataService = $this->app->get(ApplicationDataService::class);
        $this->assertEquals(
            MockedBankAccountRepository::BANK_HOLDER_SUGGESTION,
            $applicationDataService->decryptForApplicantStage(
                $application,
                $application->applicationSurePayResult->encrypted_name_suggestion
            )
        );

        $body = new FieldValidationParams(
            (object) [
                $bankAccountField->code => MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_CLOSE_MATCH,
                $bankAccountHolderField->code => $this->faker->lastname,
            ]
        );

        $applicationDataService->saveApplicationStageData(
            $applicationStage1Sequence3,
            $body->data,
            submit: true,
        );

        $this->flowService->submitApplicationStage($applicationStage1Sequence3);
        $surePayService->checkSurePayForApplication($application);

        $this->assertDatabaseHas(ApplicationSurePayResult::class, [
            'application_id' => $application->id,
        ]);

        $application->refresh();

        $this->assertEquals(
            MockedBankAccountRepository::BANK_HOLDER_SUGGESTION,
            $applicationDataService->decryptForApplicantStage(
                $application,
                $application->applicationSurePayResult->encrypted_name_suggestion
            )
        );
    }
}
