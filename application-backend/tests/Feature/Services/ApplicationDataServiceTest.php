<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use _PHPStan_adbc35a1c\Nette\Neon\Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\MockedBankAccountRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ValidationErrorException;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\SubsidyStashFieldHasher;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FieldValidationParams;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHashField;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

/**
 * @group application
 * @group application-data-service
 */
class ApplicationDataServiceTest extends TestCase
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
    private string $keyPair;
    private ClientPublicKey $publicKey;
    private ResponseEncryptionService $responseEncryptionService;


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

        SubsidyStageTransition::factory()
            ->for($this->subsidyStage1, 'currentSubsidyStage')
            ->for($this->subsidyStage2, 'targetSubsidyStage')
            ->create(['target_application_status' => ApplicationStatus::Submitted]);

        $this->identity = Identity::factory()->create();
    }

    public static function validateFieldsPerTypeDataProvider()
    {
        $faker = Faker::create();
        return [
            'Text field' => [
                FieldType::Text,
                $faker->name(),
            ],
            'Textarea field' => [
                FieldType::TextArea,
                $faker->text(),
            ],
            'Email field' => [
                FieldType::TextEmail,
                $faker->freeEmail(),
            ],
        ];
    }

    /**
     * @group validation-required
     * @dataProvider validateFieldsPerTypeDataProvider
     */
    public function testValidateEmptyRequiredFieldOnSubmit(FieldType $fieldType, string $value): void
    {
        $field =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                    'code' => $this->faker->word,
                    'type' => $fieldType,
                    'is_required' => true
                 ]);

        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();

        $body = new FieldValidationParams(
            (object)[
                $field->code => null,
            ]
        );

        try {
            $this->app->get(ApplicationDataService::class)->validateFieldValues(
                $applicationStage,
                $body->data,
                submit: true,
            );
        } catch (ValidationErrorException $e) {
            $this->assertEquals($e->getMessage(), 'Validation error in validation result!');
            $this->assertEquals(
                Str::lower(sprintf('%s is verplicht.', $field->code)),
                Str::lower($e->getValidationResults()[$field->code][0]->message)
            );
        }
    }

    /**
     * @group validation-required
     * @dataProvider validateFieldsPerTypeDataProvider
     */
    public function testValidateEmptyRequiredFieldOnSave(FieldType $fieldType, string $value): void
    {
        $field =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                             'code' => $this->faker->word,
                             'type' => $fieldType,
                             'is_required' => true
                         ]);

        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();

        $body = new FieldValidationParams(
            (object)[
                $field->code => null,
            ]
        );

        $validationResult = $this->app->get(ApplicationDataService::class)->validateFieldValues(
            $applicationStage,
            $body->data,
            submit: false,
        );
        $this->assertEmpty($validationResult);
    }

    /**
     * @group validation-required
     * @dataProvider validateFieldsPerTypeDataProvider
     */
    public function testValidateFilledRequiredFieldOnSave(FieldType $fieldType, string $value): void
    {
        $field =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                             'code' => $this->faker->word,
                             'type' => $fieldType,
                             'is_required' => true
                         ]);

        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();

        $body = new FieldValidationParams(
            (object)[
                $field->code => $value,
            ]
        );

        $validationResult = $this->app->get(ApplicationDataService::class)->validateFieldValues(
            $applicationStage,
            $body->data,
            submit: false,
        );
        $this->assertEmpty($validationResult);
    }

    /**
     * @group validation-required
     * @dataProvider validateFieldsPerTypeDataProvider
     */
    public function testValidateFilledRequiredFieldOnSubmit(FieldType $fieldType, string $value): void
    {
        $field =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                             'code' => $this->faker->word,
                             'type' => $fieldType,
                             'is_required' => true
                         ]);

        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();

        $body = new FieldValidationParams(
            (object)[
                $field->code => $value,
            ]
        );

        $validationResult = $this->app->get(ApplicationDataService::class)->validateFieldValues(
            $applicationStage,
            $body->data,
            submit: true,
        );
        $this->assertEmpty($validationResult);
    }

    public static function validateBankAccountFieldsDataProvider(): array
    {
        $faker = Faker::create();
        return [
            "Empty bankaccount number" => [
                '',
                $faker->name(),
            ],
            "Empty bankHolderName" => [
                $faker->iban('nl'),
                '',
            ],
            "Bankaccount number null" => [
                null,
                $faker->name(),
            ],
            "Bankaccount holder null" => [
                $faker->iban('nl'),
                null,
            ],
            "Bankaccount holder and name null" => [
                null,
                null,
            ]
        ];
    }

    /**
     * @dataProvider validateBankAccountFieldsDataProvider
     * @group validate-surepay
     * @group validation
     */
    public function testValidateEmptyBankAccountFieldsWithBankAccountOnSubmit(
        ?string $bankAccountNumber,
        ?string $bankAccountHolder,
    ): void {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();
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

        $params = [
            $bankAccountField->code => $bankAccountNumber,
            $bankAccountHolderField->code => $bankAccountHolder,
        ];

        $body = new FieldValidationParams(
            (object) $params
        );


        try {
            $this->app->get(ApplicationDataService::class)->validateFieldValues(
                $applicationStage,
                $body->data,
                submit: true,
            );
        } catch (ValidationErrorException $e) {
            $this->assertEquals($e->getMessage(), 'Validation error in validation result!');
            if (empty($bankAccountNumber)) {
                $this->assertEquals(
                    'bank account number is verplicht.',
                    Str::lower($e->getValidationResults()[$bankAccountField->code][0]->message)
                );
            }
            if (empty($bankAccountHolder)) {
                $this->assertEquals(
                    'bank account holder is verplicht.',
                    Str::lower($e->getValidationResults()[$bankAccountHolderField->code][0]->message)
                );
            }
        }
    }

    /**
     * @group field-hash
     */
    public function testSingleFieldHashCreationAfterSave(): void
    {
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

        $params = [
            $bankAccountField->code => MockedBankAccountRepository::BANK_ACCCOUNT_NUMBER_MATCH,
        ];

        $body = new FieldValidationParams(
            (object) $params
        );

        $this->app->get(ApplicationDataService::class)->saveApplicationStageData(
            $applicationStage,
            $body->data,
            submit: true,
        );

        $this->assertDatabaseHas(ApplicationHash::class, [
            'subsidy_stage_hash_id' => $subsidyStageHash->id,
            'application_id' => $application->id
        ]);
    }

    /**
     * @group field-hash
     */
    public function testMultiFieldHashCreationAfterSave(): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();
        $postalCodeField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                'code' => 'postalCode',
                'type' => FieldType::CustomPostalCode,
            ]);

        $houseNumberField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                'code' => 'houseNumber',
                'type' => FieldType::TextNumeric,
            ]);

        $houseNumberAdditionField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                'code' => 'houseNumberAddition',
                'type' => FieldType::Text,
            ]);

        $subsidyStageHash = SubsidyStageHash::factory()
            ->for($this->subsidyStage1)
            ->create();

        SubsidyStageHashField::factory()
            ->for($subsidyStageHash)
            ->for($postalCodeField)
            ->create();

        SubsidyStageHashField::factory()
            ->for($subsidyStageHash)
            ->for($houseNumberField)
            ->create();

        SubsidyStageHashField::factory()
            ->for($subsidyStageHash)
            ->for($houseNumberAdditionField)
            ->create();

        $params = [
            $postalCodeField->code => $this->faker->postcode(),
            $houseNumberField->code => $this->faker->randomNumber(),
            $houseNumberAdditionField->code => $this->faker->randomElement(['A', 'B', 'C', '', '1', '2', '3']),
        ];

        $fieldValuesCollection = collect([
            $postalCodeField->code => new FieldValue($postalCodeField, $params[$postalCodeField->code]),
            $houseNumberField->code => new FieldValue($houseNumberField, (string)$params[$houseNumberField->code]),
            $houseNumberAdditionField->code => new FieldValue($houseNumberAdditionField, $params[$houseNumberAdditionField->code]),
        ])->sort(fn(FieldValue $fieldValue) => $fieldValue->field->id);

        $body = new FieldValidationParams(
            (object) $params
        );

        $this->app->get(ApplicationDataService::class)->saveApplicationStageData(
            $applicationStage,
            $body->data,
            submit: true,
        );

        /** @var SubsidyStashFieldHasher $hasher */
        $hasher = $this->app->make(SubsidyStashFieldHasher::class);

        $this->assertDatabaseHas(ApplicationHash::class, [
            'subsidy_stage_hash_id' => $subsidyStageHash->id,
            'application_id' => $application->id,
            'hash' => $hasher->makeApplicationFieldHash($subsidyStageHash, $fieldValuesCollection->toArray(), $applicationStage),
        ]);

    }
}
