<?php

declare(strict_types=1);

namespace Feature\Services;

use Carbon\Carbon;
use MinVWS\DUSi\Assessment\API\Services\BankAccountDuplicatesService;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\BankAccountSubsidyStageHashNotFoundException;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Test\MocksEncryption;

/**
 * @group bankaccount-duplicates
 */
class BankAccountDuplicatesServiceTest extends TestCase
{
    use MocksEncryption;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStageHash $bankAccountSubsidyStageHash;
    private Identity $identity;
    private Application $application;

    private BankAccountDuplicatesService $bankAccountDuplicatesService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create(['status' => VersionStatus::Published]);
        $this->subsidyStage1 = SubsidyStage::factory()->for($this->subsidyVersion)->create([
            'stage' => 1,
            'subject_role' => SubjectRole::Applicant
        ]);

        $this->identity = Identity::factory()->create();

        $this->bankAccountDuplicatesService = $this->app->get(BankAccountDuplicatesService::class);
    }

    public function createApplicationWithBankAccountNumberHash(string $hash): Application
    {
        $application = Application::factory()
            ->forIdentity($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'updated_at' => Carbon::today(),
                'created_at' => Carbon::today(),
                'final_review_deadline' => Carbon::tomorrow(),
                'status' => ApplicationStatus::Submitted,
            ]);

        ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create(
            ['is_current' => false, 'is_submitted' => true, 'submitted_at' => Carbon::now()]
        );

        ApplicationHash::factory()
            ->for($this->bankAccountSubsidyStageHash)
            ->for($application)
            ->create(['hash' => $hash]);

        return $application;
    }

    public function createBankAccountSubsidyStageHash(SubsidyStage $subsidyStage): SubsidyStageHash
    {
        return SubsidyStageHash::factory()
            ->for($subsidyStage)->create([
                  'name' => BankAccountDuplicatesService::BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME,
              ]);
    }

    public function testBankAccountDuplicatesHashNotFound(): void
    {
        $this->expectException(BankAccountSubsidyStageHashNotFoundException::class);

        $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($this->subsidy);
    }

    public function testBankAccountDuplicatesFound(): void
    {
        $this->bankAccountSubsidyStageHash = $this->createBankAccountSubsidyStageHash($this->subsidyStage1);
        $application1 = $this->createApplicationWithBankAccountNumberHash('hash1');
        $application2 = $this->createApplicationWithBankAccountNumberHash('hash1');

        $duplicates = $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($this->subsidy);

        $this->assertCount(1, $duplicates);
        $this->assertContains(
            $application1->id,
            $duplicates->first()->applications->map(fn(Application $application) => $application->id)
        );
        $this->assertContains(
            $application2->id,
            $duplicates->first()->applications->map(fn(Application $application) => $application->id)
        );
    }

    public function testBankAccountDuplicatesNotFound(): void
    {
        $this->bankAccountSubsidyStageHash = $this->createBankAccountSubsidyStageHash($this->subsidyStage1);
        $this->createApplicationWithBankAccountNumberHash('hash1');
        $this->createApplicationWithBankAccountNumberHash('hash2');

        $duplicates = $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($this->subsidy);

        $this->assertCount(0, $duplicates);
    }

    public function testMultipleBankAccountDuplicatesFound(): void
    {
        $this->bankAccountSubsidyStageHash = $this->createBankAccountSubsidyStageHash($this->subsidyStage1);
        $this->createApplicationWithBankAccountNumberHash('hash1');
        $this->createApplicationWithBankAccountNumberHash('hash2');
        $this->createApplicationWithBankAccountNumberHash('hash1');
        $this->createApplicationWithBankAccountNumberHash('hash2');

        $duplicates = $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($this->subsidy);

        $this->assertCount(2, $duplicates);
    }

    public function testBankAccountDuplicatesServiceWhenMultipleSubsidyHashesExist(): void
    {
        $this->bankAccountSubsidyStageHash = $this->createBankAccountSubsidyStageHash($this->subsidyStage1);

        $this->createAnotherSubsidyWithSubsidyHash();

        $application1 = $this->createApplicationWithBankAccountNumberHash('hash1');
        $application2 = $this->createApplicationWithBankAccountNumberHash('hash1');

        $duplicates = $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($this->subsidy);

        $this->assertCount(1, $duplicates);
        $this->assertContains(
            $application1->id,
            $duplicates->first()->applications->map(fn(Application $application) => $application->id)
        );
        $this->assertContains(
            $application2->id,
            $duplicates->first()->applications->map(fn(Application $application) => $application->id)
        );
    }

    private function createAnotherSubsidyWithSubsidyHash(): void
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()
            ->for($subsidy)
            ->create(['status' => VersionStatus::Published]);
        $subsidyStage = SubsidyStage::factory()->for($subsidyVersion)->create([
            'stage' => 1,
            'subject_role' => SubjectRole::Applicant
        ]);

        $identity = Identity::factory()->create();

        $this->createBankAccountSubsidyStageHash($subsidyStage);
    }
}
