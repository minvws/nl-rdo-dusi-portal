<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use MinVWS\DUSi\Shared\Application\Models\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Application\Models\IdentityType;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;

/**
 * @group application
 * @group application-repository
 */
class ApplicationReferenceRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private SubsidyVersion $subsidyVersion;
    private ApplicationRepository $applicationRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationRepository = app(ApplicationRepository::class);

        $subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->recycle($subsidy)
            ->create();

    }

    public function testApplicationReference(): void
    {
        $application = $this->getApplication();
        $this->applicationRepository->saveApplication($application);

        $this->assertNotEmpty($application->reference);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{6}-\d{8}$/', $application->reference);
    }

    public function testApplicationReferencePassesTheElevenRule(): void
    {
        $application = $this->getApplication();
        $this->applicationRepository->saveApplication($application);

        $referenceNumber = substr($application->reference, 7);
        $this->assertTrue((int)$referenceNumber % 11 === 0);
    }

    public function testApplicationReferenceDuplicateExceptionHandling(): void
    {
        $this->markTestSkipped();
    }

    public function testApplicationReferenceCreationMaxTries(): void
    {
        $this->markTestSkipped();

    }

    private function getApplication(): Application
    {
        $application = new Application();
        $application->subsidyVersion()->associate($this->subsidyVersion);
        $application->application_title = $this->faker->title;
        $application->identity_type = IdentityType::EncryptedCitizenServiceNumber;
        $application->identity_identifier = IdentityType::EncryptedCitizenServiceNumber;

        return $application;
    }
}
