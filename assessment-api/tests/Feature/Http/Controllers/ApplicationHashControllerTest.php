<?php

declare(strict_types=1);

namespace Feature\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use MinVWS\DUSi\Assessment\API\Services\BankAccountDuplicatesService;
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
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @group application-list
 */
class ApplicationHashControllerTest extends TestCase
{
    use MocksEncryption;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStageHash $bankAccountSubsidyStageHash;
    private Identity $identity;
    private Application $application;

    private Authenticatable $implementationCoordinatorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create(['status' => VersionStatus::Published]);
        $this->subsidyStage1 = SubsidyStage::factory()
            ->for($this->subsidyVersion)
            ->create([
               'stage' => 1,
               'subject_role' => SubjectRole::Applicant
           ]);

        $this->identity = Identity::factory()->create();

        $this->implementationCoordinatorUser = User::factory()->create();
        $this->implementationCoordinatorUser->attachRole(RoleEnum::ImplementationCoordinator, $this->subsidy->id);
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

    public function createBankAccountSubsidyStageHash(): void
    {
        $this->bankAccountSubsidyStageHash = SubsidyStageHash::factory()
            ->for($this->subsidyStage1)->create([
                'name' => BankAccountDuplicatesService::BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME,
            ]);
    }

    public function testBankAccountDuplicates(): void
    {
        $this->createBankAccountSubsidyStageHash();
        $this->createApplicationWithBankAccountNumberHash(
            '1151a36e738c7e5933927387b1a55931ce4fbcfc45c8197476d6050aac5b59a7'
        );
        $this->createApplicationWithBankAccountNumberHash(
            '1151a36e738c7e5933927387b1a55931ce4fbcfc45c8197476d6050aac5b59a7'
        );

        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', sprintf('/api/subsidies/%s/bankaccounts/duplicates', $this->subsidy->id));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');

        $response->assertJsonFragment(['hash' => '1151a36e738c7e5933927387b1a55931ce4fbcfc45c8197476d6050aac5b59a7']);
    }
}
