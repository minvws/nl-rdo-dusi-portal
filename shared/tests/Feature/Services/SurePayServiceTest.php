<?php

declare(strict_types=1);

namespace Feature\Services;

use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\MockedBankAccountRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\SurePayService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Test\MocksEncryption;
use MinVWS\DUSi\Shared\Tests\TestCase;

/**
 * @group surepay
 */
class SurePayServiceTest extends TestCase
{
    use WithFaker;
    use MocksEncryption;

    private const SUBSIDY_PZCM_ID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

    private readonly SurePayService $surePayService;
    private ApplicationStageEncryptionService $encryptionService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupMocksEncryption();

        $this->surePayService = $this->app->get(SurePayService::class);
        $this->encryptionService = $this->app->get(ApplicationStageEncryptionService::class);

        $this->subsidy = Subsidy::factory()->create([
            'id' => self::SUBSIDY_PZCM_ID,
        ]);
        $this->subsidyVersion =
            SubsidyVersion::factory()
                ->for($this->subsidy)
                ->create(['status' => VersionStatus::Published]);

        $this->subsidyStage1 = SubsidyStage::factory()->for($this->subsidyVersion)->create();
        $this->subsidyStage2 =
            SubsidyStage::factory()
                ->for($this->subsidyVersion)
                ->create(['stage' => 2, 'subject_role' => SubjectRole::Assessor]);

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage1, 'currentSubsidyStage')
            ->for($this->subsidyStage2, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Submitted]);

        $this->identity = Identity::factory()->create();
    }

    /**
     * @group surepay-encryption
     */
    public function testCheckSurePayForApplicationShouldStoreEncryptedNameSuggestion(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();

        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage = ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStage1)
            ->create([
                'encrypted_key' => $encryptedKey
            ]);

        $bankAccountField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                         'code' => 'bankAccountNumber',
                         'type' => FieldType::CustomBankAccount,
                     ]);
        $bankAccountHolderField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                         'code' => 'bankAccountHolder',
                         'type' => FieldType::Text,
                     ]);

        $encrypter = $this->encryptionService->getEncrypter($applicationStage);
        $encryptedValue = $encrypter->encrypt(
            MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_CLOSE_MATCH
        );
        Answer::factory()
            ->for($bankAccountField)
            ->for($applicationStage)
            ->create([
                 'encrypted_answer' => $encryptedValue,
            ]);
        Answer::factory()
            ->for($bankAccountHolderField)
            ->for($applicationStage)
            ->create([
                 'encrypted_answer' => $encryptedValue,
            ]);

        $applicationSurePayResult = $this->surePayService->checkSurePayForApplication($application);

        $this->assertDatabaseHas(ApplicationSurePayResult::class, [
            'application_id' => $application->id,
            'encrypted_name_suggestion' => $encrypter->encrypt(MockedBankAccountRepository::BANK_HOLDER_SUGGESTION),
            'created_at' => $applicationSurePayResult->created_at->format('Y-m-d H:i:s')
        ]);

        $this->assertEquals(
            $applicationSurePayResult->encrypted_name_suggestion,
            $encrypter->encrypt(MockedBankAccountRepository::BANK_HOLDER_SUGGESTION)
        );
    }
}
