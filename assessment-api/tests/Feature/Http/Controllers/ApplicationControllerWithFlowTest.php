<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Test\AbstractSubsidyAggregateManager;
use MinVWS\DUSi\Shared\Test\ApplicationCreator;
use MinVWS\DUSi\Shared\Test\ComplexSubsidyAggregateManager;
use MinVWS\DUSi\Shared\Test\MocksEncryption;

class ApplicationControllerWithFlowTest extends TestCase
{
    use MocksEncryption;

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
        $this->subsidyManager->build();
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
            AbstractSubsidyAggregateManager::VALUE_APPROVED
        );
        $applicationStage3 = $this->flowService->submitApplicationStage($applicationStage2);

        $this->applicationRepository->assignApplicationStage($applicationStage3, $assessor2);

        $secondAssessmentField = $this->subsidyManager->getSubsidyStageField('secondAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage3,
            $secondAssessmentField,
            AbstractSubsidyAggregateManager::AGREE_WITH_FIRST_ASSESSMENT
        );

        $applicationStage4 = $this->flowService->submitApplicationStage($applicationStage3);
        $this->applicationRepository->assignApplicationStage($applicationStage3, $internalAuditor);

        $internalAssessmentField = $this->subsidyManager->getSubsidyStageField('internalAssessment');
        $this->subsidyManager->createAnswer(
            $applicationStage4,
            $internalAssessmentField,
            AbstractSubsidyAggregateManager::VALUE_REJECTED
        );

        $currentApplicationStage = $this->flowService->submitApplicationStage($applicationStage4);
        $this->assertEquals($currentApplicationStage->assessor_user_id, $assessor1->id);

        $this->subsidyManager->createAnswer(
            $currentApplicationStage,
            $firstAssessmentField,
            AbstractSubsidyAggregateManager::VALUE_APPROVED
        );
        $nextApplicationStage = $this->flowService->submitApplicationStage($currentApplicationStage);

        $this->assertEquals(3, $nextApplicationStage->subsidyStage->stage);

        $response = $this
            ->be($assessor2)
            ->json('PUT', '/api/applications/' . $application->id . '/assessor')
        ;
        $response->assertStatus(200);
    }
}
