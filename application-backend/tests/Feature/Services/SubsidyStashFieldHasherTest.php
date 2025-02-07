<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use LogicException;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Services\SubsidyStashFieldHasher;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHashField;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;

/**
 * @group field-hash
 */
class SubsidyStashFieldHasherTest extends TestCase
{
    use WithFaker;
    use MocksEncryptionAndHashing;

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

        $this->identity = Identity::factory()->create();
    }

    public function testFieldHashWithNoDataShouldThrowException(): void
    {
        $this->expectException(LogicException::class);

        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();
        $bankAccountField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                         'code' => 'bankAccountNumber',
                         'type' => FieldType::CustomBankAccount,
                     ]);

        $subsidyStageHash = SubsidyStageHash::factory()
            ->for($this->subsidyStage1)
            ->create();

        SubsidyStageHashField::factory()
            ->for($subsidyStageHash)
            ->for($bankAccountField)
            ->create();

        $fieldValuesCollection = [];
        $fieldValuesCollection[$bankAccountField->code] = new FieldValue($bankAccountField, null);

        /** @var SubsidyStashFieldHasher $hasher */
        $hasher = $this->app->make(SubsidyStashFieldHasher::class);

        $hasher->makeApplicationFieldHash($subsidyStageHash, $fieldValuesCollection, $applicationStage);
    }
}
