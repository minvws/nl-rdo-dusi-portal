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
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationSaveBody;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use Throwable;

class PCZMApplicationSeeder extends Seeder
{
    private Generator $faker;

    public const PCZM_VERSION_UUID = '513011cd-789b-4628-ba5c-2fee231f8959';
    public const PCZM_STAGE_1_UUID = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
    public const PCZM_STAGE_2_UUID = '8027c102-93ef-4735-ab66-97aa63b836eb';
    public function __construct(
        protected ApplicationDataService $applicationDataService,
        protected ApplicationFileManager $fileManager,
        protected ApplicationStageEncryptionService $encryptionService,
        protected HsmEncryptionService $hsmEncryptionService,
        protected IdentityService $identityService,
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
        [$encrypted_key] = $this->encryptionService->generateEncryptionKey();
        return ApplicationStage::factory()->create(
            [
                'application_id' => $application->id,
                'sequence_number' => $sequence,
                'is_submitted' => $isSubmitted,
                'subsidy_stage_id' => $subsidyStageId,
                'encrypted_key' => $encrypted_key,
                'is_current' => $isCurrent,
            ]
        );
    }

    /**
     * @throws Exception
     */
    private function writeFields($app_stage): void
    {
        // IDs are hardcoded because they are provided in pczmApplicationData.json
        $this->writeField($app_stage, 'Gewaarmerkt verzekeringsbericht', '739cecda-0aa3-4692-a5e7-040984d5ff2a');
        $this->writeField($app_stage, 'WIA-Beslissing', '337016e7-20e8-4d5e-9f20-1999980d4b5c');
        $this->writeField($app_stage, 'Toekenningsbrief', '1dbbc21d-8c2b-4075-bf1e-7bd208006117');
        $this->writeField($app_stage, 'Bewijs dienstverband', 'dbef8055-7c3e-4025-89fb-a1e5e1a73f98');
        $this->writeField($app_stage, 'Medisch onderzoeksverslag', '89040bab-5f11-40ec-9a2c-57081acbeb4c');
    }

    /**
     * @throws Exception
     */
    private function writeField($app_stage, $title, $fileId): void
    {
        $content = file_get_contents(__DIR__ . "/resources/nvt.pdf");

        $field = $app_stage->subsidyStage->fields->filter(function ($field) use ($title) {
            return $field->type === FieldType::Upload && $field->title === $title;
        })->first();
        $this->fileManager->writeFile(
            $app_stage,
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
    private function createApplicationData($application, $applicationData): void
    {
        $applicationStage = $this->createApplicationStage(
            $application, 1, true, self::PCZM_STAGE_1_UUID, false
        );
        $this->writeFields($applicationStage);

        $this->applicationDataService->saveApplicationStageData($applicationStage, $applicationData, true);

        $this->createApplicationSurepayResult($applicationData, $application, $applicationStage);

        $this->createApplicationStage(
            $application,
            2,
            false,
            self::PCZM_STAGE_2_UUID,
            true);
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
        $application_data = $this->readApplicationDataFromFile();
        Application::factory(
            [
                'application_title' => 'DUSi Subsidie Admin API',
                'judgement' => 'pending',
                'final_review_deadline' => now()->addMonth(),
                'subsidy_version_id' => self::PCZM_VERSION_UUID,
                'status' => ApplicationStatus::Pending
            ]
        )->for($this->createIdentifier())
            ->count($count)
            ->create()
            ->each(function ($application) use ($application_data) {
                $application_data->bankAccountNumber = Arr::random(MockedBankAccountRepository::allValid());
                $this->createApplicationData($application, $application_data);

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
                MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_TOO_SHORT => NameMatchResult::NameTooShort,
                default => NameMatchResult::CouldNotMatch
            },
        ];
        if ($applicationData->bankAccountNumber === MockedBankAccountRepository::BANK_ACCOUNT_NUMBER_CLOSE_MATCH) {
            $applicationSurepayResult['encrypted_name_suggestion'] =
                $this->applicationDataService->encryptForStage($applicationStage, $this->faker->lastname);
        }

        ApplicationSurePayResult::factory()->for($application)->create($applicationSurepayResult);
    }


}
