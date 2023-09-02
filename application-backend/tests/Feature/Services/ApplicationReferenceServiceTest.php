<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Shared\Application\Models\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Models\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use Throwable;

/**
 * @group application
 * @group application-reference-service
 */
class ApplicationReferenceServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private ApplicationService $applicationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationService = app(ApplicationService::class);
    }

    public function testAppliationReferenceIsCreated()
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()
            ->recycle($subsidy)
            ->create();
        $subsidyStage = SubsidyStage::factory()
            ->recycle($subsidyVersion)
            ->create();

        $application = $this->applicationService->createApplication(
            new Identity(IdentityType::EncryptedCitizenServiceNumber, $this->faker->word()),
            $subsidyStage
        );

        $this->assertNotEmpty($application->reference);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{6}-\d{8}$/',$application->reference);
    }
}
