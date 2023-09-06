<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Database\QueryException;
use MinVWS\DUSi\Application\Backend\Services\ApplicationReferenceGenerator;
use MinVWS\DUSi\Application\Backend\Services\ApplicationReferenceService;
use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Application\Backend\Services\Exceptions\ApplicationReferenceException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group application
 * @group application-reference-service
 */
class ApplicationReferenceServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private ApplicationService $applicationService;
    private SubsidyVersion $subsidyVersion;
    private int $callCount;

    protected function setUp(): void
    {

        parent::setUp();

        $this->callCount = 0;

        $this->applicationService = app(ApplicationService::class);

        $subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->recycle($subsidy)
            ->create();
    }

    public function testAppliationReferenceIsCreated()
    {
        $application = $this->createSubsidyApplication();

        $this->assertNotEmpty($application->reference);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{6}-\d{8}$/', $application->reference);
    }

    public function testApplicationReferencePassesTheElevenRule(): void
    {
        $application = $this->createSubsidyApplication();

        $referenceNumber = substr($application->reference, 7);
        $this->assertTrue((int)$referenceNumber % 11 === 0);
    }

    public function testApplicationReferenceDuplicateExceptionThrown(): void
    {
        $elevenRuleNumber = 1122334;
        $reference = sprintf('%s-%s', $this->subsidyVersion->subsidy->reference_prefix, $elevenRuleNumber);

        // Create a mock for the ApplicationReferenceGenerator class with only
        $generatorMock = $this->getMockBuilder(ApplicationReferenceService::class)
            ->setConstructorArgs([
                $this->app->make(ApplicationRepository::class), $this->app->make(ApplicationReferenceGenerator::class)])
            ->onlyMethods(['generateUniqueReferenceByElevenRule'])
            ->getMock();

        // Configure the mock method for the first call
        $generatorMock
            ->expects($this->atLeastOnce())
            ->method('generateUniqueReferenceByElevenRule')
            ->willReturn($reference);

        // Bind the mock into the Laravel's service container
        $this->app->instance(ApplicationReferenceService::class, $generatorMock);

        //Fixture application
        Application::factory([
            'reference' => $reference,
        ])->recycle($this->subsidyVersion)->create();

        $this->applicationService = $this->app->make(ApplicationService::class);

        $this->expectException(QueryException::class);

        //Create another application
        $this->createSubsidyApplication();
    }

    public function testApplicationReferenceDuplicateExceptionHandling(): void
    {
        $elevenRuleNumber = 1122334;
        $reference = sprintf('%s-%s', $this->subsidyVersion->subsidy->reference_prefix, $elevenRuleNumber);

        $newElevenRuleNumber = 12345678;
        $newReference = sprintf('%s-%s', $this->subsidyVersion->subsidy->reference_prefix, $newElevenRuleNumber);

        // Create a mock for the ApplicationReferenceGenerator class with only
        $generatorMock = $this->getMockBuilder(ApplicationReferenceService::class)
            ->setConstructorArgs([
                $this->app->make(ApplicationRepository::class), $this->app->make(ApplicationReferenceGenerator::class)])
            ->onlyMethods(['generateUniqueReferenceByElevenRule'])
            ->getMock();

        // Configure the mock method for the first call
        $generatorMock
            ->expects($this->atLeastOnce())
            ->method('generateUniqueReferenceByElevenRule')
            ->willReturnCallback(function () use ($reference, $newReference) {
                if ($this->callCount === 0) {
                    $this->callCount++;
                    return $reference;
                } else {
                    $this->callCount++;
                    return $newReference;
                }
            });

        // Bind the mock into the Laravel's service container
        $this->app->instance(ApplicationReferenceService::class, $generatorMock);

        //Fixture application
        Application::factory([
            'reference' => $reference,
        ])->recycle($this->subsidyVersion)->create();

        $this->applicationService = $this->app->make(ApplicationService::class);

        //Create another application, which will trigger generateUniqueReferenceByElevenRule
        $newApplication = $this->createSubsidyApplication();
        $this->assertEquals($newReference, $newApplication->reference);
    }

    public function testApplicationReferenceCreationReachingMaxTriesShouldThrowAnException(): void
    {
        $elevenRuleNumber = 1122334;

        // Create a mock for the ApplicationReferenceGenerator class with only
        $generatorMock = $this->getMockBuilder(ApplicationReferenceGenerator::class)
            ->onlyMethods(['generateRandomNumberByElevenRule'])
            ->getMock();

        $generatorMock->expects($this->any())
            ->method('generateRandomNumberByElevenRule')
            ->willReturn($elevenRuleNumber);

        // Bind the mock into the Laravel's service container
        $this->app->instance(ApplicationReferenceGenerator::class, $generatorMock);

        $this->expectException(ApplicationReferenceException::class);

        Application::factory([
            'reference' => sprintf('%s-%08d', $this->subsidyVersion->subsidy->reference_prefix, $elevenRuleNumber),
        ])->recycle($this->subsidyVersion)->create();

        $this->applicationService = $this->app->make(ApplicationService::class);

        // Create another application when the generateRandomNumberByElevenRule should be called
        $this->createSubsidyApplication();
    }

    public function testApplicationReferenceShouldHaveLeadingZeros(): void
    {
        $elevenRuleNumber = 11;

        // Create a mock for the ApplicationReferenceGenerator class with only
        $generatorMock = $this->getMockBuilder(ApplicationReferenceGenerator::class)
            ->onlyMethods(['generateRandomNumberByElevenRule'])
            ->getMock();

        $generatorMock->expects($this->any())
            ->method('generateRandomNumberByElevenRule')
            ->willReturn($elevenRuleNumber);

        // Bind the mock into the Laravel's service container
        $this->app->instance(ApplicationReferenceGenerator::class, $generatorMock);

        $this->applicationService = $this->app->make(ApplicationService::class);

        // Create another application when the generateRandomNumberByElevenRule should be called
        $application = $this->createSubsidyApplication();
        $this->assertEquals(
            sprintf('%s-%s', $this->subsidyVersion->subsidy->reference_prefix, '00000011'),
            $application->reference
        );
    }

    private function createSubsidyApplication(): Application
    {
        $subsidyStage = SubsidyStage::factory()
            ->recycle($this->subsidyVersion)
            ->create();

        // TODO: this test unnecessarily exposes the createApplication method
        return $this->applicationService->createApplication(
            Uuid::uuid4()->toString(),
            new Identity(IdentityType::EncryptedCitizenServiceNumber, $this->faker->word()),
            $subsidyStage
        );
    }
}
