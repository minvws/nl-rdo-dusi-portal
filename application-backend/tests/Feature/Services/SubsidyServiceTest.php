<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use MinVWS\DUSi\Application\Backend\Services\SubsidyService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyOverview;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyOverviewParams;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

/**
 * @group subsidy
 * @group subsidy-service
 */
class SubsidyServiceTest extends TestCase
{
    use WithFaker;
    use MocksEncryptionAndHashing;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStage $subsidyStage2;
    private Identity $identity;
    private Field $textField;
    private Field $uploadField;
    private Field $bankAccountHolderField;
    private Field $bankAccountNumber;
    private string $keyPair;
    private ClientPublicKey $publicKey;
    private ResponseEncryptionService $responseEncryptionService;
    private SubsidyService $subsidyService;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->withoutFrontendEncryption();

        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion =
            SubsidyVersion::factory()
                ->for($this->subsidy)
                ->create(['status' => VersionStatus::Published]);
        $this->subsidyStage1 = SubsidyStage::factory()->for($this->subsidyVersion)->create();

        $this->subsidyStage2 =
            SubsidyStage::factory()
                ->for($this->subsidyVersion)
                ->create(['stage' => 2, 'subject_role' => SubjectRole::Assessor]);

        $this->identity = Identity::factory()->create();

        $this->keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($this->keyPair);
        $this->publicKey = new ClientPublicKey($publicKey);

        Storage::fake(Disk::APPLICATION_FILES);

        $this->responseEncryptionService = $this->app->make(ResponseEncryptionService::class);
        $this->subsidyService = $this->app->make(SubsidyService::class);
    }


    public function testGetSubsidyOverviewEmpty(): void
    {
        $params = new SubsidyOverviewParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->subsidyService->getSubsidyOverview($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $subsidyOverview = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, SubsidyOverview::class, $this->keyPair);
        $this->assertNotNull($subsidyOverview);
        $this->assertNotNull($subsidyOverview->subsidy);
        $this->assertIsArray($subsidyOverview->applications);
        $this->assertCount(0, $subsidyOverview->applications);
        $this->assertEquals($this->subsidy->code, $subsidyOverview->subsidy->code);
    }

    public function testGetSubsidyOverview(): void
    {
        $application = Application::factory()
            ->for($this->identity)
            ->for($this->subsidyVersion)
            ->create([
                'application_title' => 'some_application_title',
                'updated_at' => CarbonImmutable::now()->subDays(5),
                'created_at' => CarbonImmutable::now()->subDays(10),
                'status' => ApplicationStatus::Draft,
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStage1)
            ->create([
                'sequence_number' => 1,
                'is_current' => true,
                'is_submitted' => false,
                'submitted_at' => null,
                'expires_at' => CarbonImmutable::tomorrow(),
            ]);

        ApplicationStage::factory()
            ->for($application)
            ->for($this->subsidyStage2)
            ->create([
                'sequence_number' => 2,
                'is_current' => false,
                'is_submitted' => false,
                'submitted_at' => null,
            ]);


        $params = new SubsidyOverviewParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $this->subsidy->code
        );

        $encryptedResponse = $this->subsidyService->getSubsidyOverview($params);
        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $subsidyOverview = $this->responseEncryptionService
            ->decryptCodable($encryptedResponse, SubsidyOverview::class, $this->keyPair);
        $this->assertNotNull($subsidyOverview);
        $this->assertNotNull($subsidyOverview->subsidy);
        $this->assertIsArray($subsidyOverview->applications);
        $this->assertCount(1, $subsidyOverview->applications);
        $this->assertEquals($application->reference, $subsidyOverview->applications[0]->reference);
    }
}
