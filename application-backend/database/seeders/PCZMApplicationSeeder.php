<?php

namespace Database\Seeders;

use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\DUSi\Application\Backend\Services\IdentityService;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\MockedBankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use Throwable;

class PCZMApplicationSeeder extends Seeder
{
    private Generator $faker;

    public const PCZM_VERSION_UUID = '513011cd-789b-4628-ba5c-2fee231f8959';
    public const PCZM_STAGE_1_UUID = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
    public const PCZM_STAGE_2_UUID = '8027c102-93ef-4735-ab66-97aa63b836eb';
    public const PCZM_STAGE_3_UUID = '61436439-E337-4986-BC18-57138E2FAB65';
    public const PCZM_STAGE_4_UUID = '7CEB3C91-5C3B-4627-B9EF-A46D5FE2ED68';
    public const PCZM_STAGE_5_UUID = '85ED726E-CDBE-444E-8D12-C56F9BED2621';
    public const PCZM_STAGE_6_UUID = 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5';

    public function __construct(
        protected ApplicationDataService $applicationDataService,
        protected ApplicationFileManager $fileManager,
        protected ApplicationStageEncryptionService $encryptionService,
        protected HsmEncryptionService $hsmEncryptionService,
        protected IdentityService $identityService,
        private readonly ApplicationFlowService $applicationFlowService,
    ) {
         $this->faker = Factory::create();
    }

    private function createApplicationStage(
        Application $application,
        int $sequence,
        bool $isSubmitted,
        string $subsidyStageId,
        bool $isCurrent
    ) {
        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        return ApplicationStage::factory()->create(
            [
                'application_id' => $application->id,
                'sequence_number' => $sequence,
                'is_submitted' => $isSubmitted,
                'subsidy_stage_id' => $subsidyStageId,
                'encrypted_key' => $encryptedKey,
                'is_current' => $isCurrent,
            ]
        );
    }

    /**
     * @throws Exception
     */
    private function writeUploadFields($appStage): void
    {
        // IDs are hardcoded because they are provided in pczmApplicationData.json
        $this->writeUploadField($appStage, 'certifiedEmploymentDocument', '739cecda-0aa3-4692-a5e7-040984d5ff2a');
        $this->writeUploadField($appStage, 'wiaDecisionDocument', '337016e7-20e8-4d5e-9f20-1999980d4b5c');
        $this->writeUploadField($appStage, 'wiaDecisionPostponedLetter', '1dbbc21d-8c2b-4075-bf1e-7bd208006117');
        $this->writeUploadField($appStage, 'employmentContract', 'dbef8055-7c3e-4025-89fb-a1e5e1a73f98');
        $this->writeUploadField($appStage, 'socialMedicalAssessment', '89040bab-5f11-40ec-9a2c-57081acbeb4c');
        $this->writeUploadField($appStage, 'bankStatement', '85046457-7794-47dd-81be-edc56aef0a0f');
    }

    /**
     * @throws Exception
     */
    private function writeUploadField($appStage, $fieldCode, $fileId): void
    {
        $content = file_get_contents(__DIR__ . "/resources/nvt.pdf");

        $field = $appStage->subsidyStage->fields()
            ->where('code', $fieldCode)
            ->where('type', FieldType::Upload)
            ->first();

        if ($field === null) {
            throw new Exception("Field $fieldCode not found in subsidy stage {$appStage->subsidy_stage_id}");
        }

        $this->fileManager->writeFile(
            $appStage,
            $field,
            $fileId,
            $content
        );
    }

    /**
     * @throws Exception
     */
    private function createIdentifier(): Identity
    {
        $identity = new EncryptedIdentity(
            IdentityType::CitizenServiceNumber,
            $this->hsmEncryptionService->encrypt(Str::random(9))
        );
        return $this->identityService->findOrCreateIdentity($identity);
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    private function createApplicationStages($application, $applicationData): void
    {
        $applicationStage = $this->createApplicationStage(
            $application, 1, false, $this->getSubsidyStageUuid(1), true
        );
        $this->writeUploadFields($applicationStage);

        $this->applicationDataService->saveApplicationStageData($applicationStage, $applicationData, true);

        $this->createApplicationSurepayResult($applicationData, $application, $applicationStage);

        $numberOfStages = $this->faker->numberBetween(2,6);

        while ($applicationStage->subsidyStage->stage <= $numberOfStages) {

            try {
                $newStage = $this->applicationFlowService->evaluateApplicationStage(
                    $applicationStage,
                    EvaluationTrigger::Submit
                );
            } catch (ApplicationFlowException $e) {
                dump(
                    sprintf(
                        'Error processing application %s: %s',
                        $applicationStage->application->reference,
                        $e->getMessage()
                    )
                );
                continue;
            }

            if ($newStage === null) {
                break;
            }

            $applicationStageData = $this->createApplicationStageData($newStage->subsidyStage->stage);
            $this->applicationDataService->saveApplicationStageData($newStage, $applicationStageData, false);
            $applicationStage = $newStage;
        }
    }

    private function readApplicationDataFromFile()
    {
        $json = file_get_contents(__DIR__ . "/resources/pczmApplicationData.json");
        return (new JSONDecoder())->decode($json)->decodeObject(ApplicationSaveBody::class)->data;
    }

    /**
     * Run the database seeds.
     * @throws Exception
     */
    public function run($count = 1): void
    {
        $applicationData = $this->readApplicationDataFromFile();
        Application::factory(
            [
                'application_title' => 'DUSi Subsidie Admin API',
                'judgement' => 'pending',
                'final_review_deadline' => now()->addMonth(),
                'subsidy_version_id' => self::PCZM_VERSION_UUID,
                'status' => ApplicationStatus::Draft
            ]
        )->for($this->createIdentifier())
            ->count($count)
            ->create()
            ->each(function ($application) use ($applicationData) {
                $applicationData->bankAccountNumber = Arr::random(MockedBankAccountRepository::allValid());
                $this->createApplicationStages($application, $applicationData);

                // Random updated_at and final_review_deadline to test sorting
                $application->final_review_deadline = now()->addDays(rand(0, 30))->startOfDay();
                $application->updated_at = now()->subDays(rand(0, 100));
                $application->save();
            });
    }

    public function createApplicationSurepayResult(
        $applicationData,
        Application $application,
        ApplicationStage $applicationStage,
    ): void
    {
        $applicationSurepayResult = [
            'name_match_result' => match ($applicationData->bankAccountNumber) {
                MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_MATCH => NameMatchResult::Match,
                MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_CLOSE_MATCH => NameMatchResult::CloseMatch,
                MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_COULD_NOT_MATCH => NameMatchResult::CouldNotMatch,
                MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_NAME_TOO_SHORT => NameMatchResult::NameTooShort,
                default => NameMatchResult::CouldNotMatch
            },
        ];
        if ($applicationData->bankAccountNumber === MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_CLOSE_MATCH) {
            $applicationSurepayResult['encrypted_name_suggestion'] =
                $this->applicationDataService->encryptForStage($applicationStage, $this->faker->lastname);
        }

        ApplicationSurePayResult::factory()->for($application)->create($applicationSurepayResult);
    }

    // Generate data for condition fields which are used on stages 2-6. There is no guarantee that the data is in line
    // with other stages.
    private function createApplicationStageData(int $currentStage): object
    {
        return (object)match ($currentStage) {
            2 => ['firstAssessment' => $this->faker->randomElement(['Goedgekeurd', 'Goedgekeurd', 'Afgekeurd'])],
            3 => ['secondAssessment' => $this->faker->randomElement(['Oneens met de eerste beoordeling', 'Eens met de eerste beoordeling', 'Eens met de eerste beoordeling'])],
            4 => ['internalAssessment' => $this->faker->randomElement(['Goedgekeurd', 'Goedgekeurd', 'Goedgekeurd', 'Afgekeurd'])],
            5 => ['implementationCoordinatorAssessment' => $this->faker->randomElement(['Goedgekeurd', 'Goedgekeurd', 'Goedgekeurd', 'Afgekeurd'])],
            6 => [],
        };
    }

    private function getSubsidyStageUuid(int $currentStage)
    {
        return match ($currentStage) {
            1 => self::PCZM_STAGE_1_UUID,
            2 => self::PCZM_STAGE_2_UUID,
            3 => self::PCZM_STAGE_3_UUID,
            4 => self::PCZM_STAGE_4_UUID,
            5 => self::PCZM_STAGE_5_UUID,
            6 => self::PCZM_STAGE_6_UUID,
            default => self::PCZM_STAGE_1_UUID,
        };
    }
}
