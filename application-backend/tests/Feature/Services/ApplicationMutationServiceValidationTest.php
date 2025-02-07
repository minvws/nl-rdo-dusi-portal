<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Application\Backend\Services\ApplicationMutationService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\MockedBankAccountRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\SurePayValidationRule;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationValidationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FieldValidationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Faker\Factory as Faker;

/**
 * @group application
 * @group application-mutation-service-validation
 */
class ApplicationMutationServiceValidationTest extends TestCase
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
            ->create(['target_application_status' => ApplicationStatus::Pending]);

        $this->identity = Identity::factory()->create();

        $this->keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($this->keyPair);
        $this->publicKey = new ClientPublicKey($publicKey);

        $this->responseEncryptionService = $this->app->make(ResponseEncryptionService::class);
    }

    public static function validateFieldsPerTypeDataProvider()
    {
        $faker = Faker::create();
        return [
            'Text field' => [
                FieldType::Text,
                $faker->name
            ],
            'Textarea field' => [
                FieldType::TextArea,
                $faker->text
            ],
        ];
    }

    /**
     * @group validation
     * @dataProvider validateFieldsPerTypeDataProvider
     */
    public function testValidateFields(FieldType $fieldType, string $value): void
    {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();

        $field =
            Field::factory()
                ->for($this->subsidyStage1)
                ->create([
                    'code' => $this->faker->name,
                    'type' => $fieldType
                 ]);


        $body = new FieldValidationParams(
            (object)[
                $field->code => $value,
            ]
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationValidationParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->validateApplication($params);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);
    }

    public static function validateFieldsDataProvider(): array
    {
        $faker = Faker::create();
        return [
            "Surepay Match" => [
                'NL62ABNA9999841479',
                $faker->name(),
                false,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => [
                        "bankAccountNumber" => [
                            [
                                "type" => "confirmation",
                                "message" => "Naam rekeninghouder komt overeen.",
                                "id" => "validationSurePayValid",
                                "params" => []
                            ]
                        ]
                    ]
                ],
            ],
            "Surepay NomMatch" => [
                'NL12ABNA9999876523',
                $faker->name(),
                false,
                EncryptedResponseStatus::UNPROCESSABLE_ENTITY,
                [
                    "validationResult" => [
                        "bankAccountNumber" => [
                            [
                                "type" => "error",
                                "message" => "Naam rekeninghouder komt niet overeen!",
                                "id" => "validationSurePayError",
                                "params" => []
                            ]
                        ]
                    ]
                ],
            ],
            "Surepay close match without bankstatement" => [
                'NL58ABNA9999142181',
                $faker->name(),
                false,
                EncryptedResponseStatus::UNPROCESSABLE_ENTITY,
                [
                    "validationResult" => [
                        "bankAccountNumber" => [
                            [
                                "type" => "error",
                                "message" =>
                                    "Naam rekeninghouder lijkt niet volledig te kloppen! Bedoelde u {suggestion}?",
                                "id" => "validationSurePayError",
                                "params" => [
                                    "suggestion" => [
                                        "code" => "bankAccountHolder",
                                        "value" => MockedBankAccountRepository::BANK_HOLDER_SUGGESTION
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                false,
            ],
            "Surepay close match with bankstatement" => [
                'NL58ABNA9999142181',
                $faker->name(),
                true,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => [
                        "bankAccountNumber" => [
                            [
                                "type" => "warning",
                                "message" =>
                                    "Naam rekeninghouder lijkt niet volledig te kloppen! Bedoelde u {suggestion}?",
                                "id" => "validationSurePayWarning",
                                "params" => [
                                    "suggestion" => [
                                        "code" => "bankAccountHolder",
                                        "value" => MockedBankAccountRepository::BANK_HOLDER_SUGGESTION
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                false,
            ],
            "Empty bankaccount number" => [
                '',
                $faker->name(),
                false,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => []
                ],
            ],
            "Empty bankHolderName" => [
                $faker->iban('nl'),
                '',
                false,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => []
                ],
            ],
            "Bankaccount number null" => [
                null,
                $faker->name(),
                false,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => []
                ],
            ],
            "Bankaccount holder null" => [
                $faker->iban('nl'),
                null,
                false,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => []
                ],
            ],
            "Bankaccount holder and name null" => [
                null,
                null,
                false,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => []
                ],
            ]
        ];
    }

    /**
     * @dataProvider validateFieldsDataProvider
     * @group validate-surepay
     * @group validation
     */
    public function testValidateFieldsWithBankAccount(
        ?string $bankAccountNumber,
        ?string $bankAccountHolder,
        bool $bankStatementIncluded,
        EncryptedResponseStatus $encryptedResponseStatus,
        array $responseBody,
    ): void {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        $applicationStage = ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();
        $bankAccountField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                'code' => SurePayValidationRule::BANK_ACCOUNT_NUMBER_FIELD,
                'type' => FieldType::CustomBankAccount,
            ]);
        $bankAccountHolderField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                'code' => SurePayValidationRule::BANK_ACCOUNT_HOLDER_FIELD,
                'type' => FieldType::Text,
            ]);
        $bankStatementField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                'code' => SurePayValidationRule::BANK_STATEMENT_FIELD,
                'type' => FieldType::Upload,
                'is_required' => false,
            ]);

        $bankStatement = null;
        if ($bankStatementIncluded) {
            $bankStatementFileId = $this->faker->uuid;
            $bankStatement = [
                ['id' => $bankStatementFileId, 'name' => $this->faker->word, 'mimeType' => 'application/pdf']
            ];
        }

        $params = [
            $bankAccountField->code => $bankAccountNumber,
            $bankAccountHolderField->code => $bankAccountHolder,
            $bankStatementField->code => $bankStatement,
        ];

        $body = new FieldValidationParams(
            (object)$params
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationValidationParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        if ($bankStatementIncluded && $bankStatementFileId) {
            $fileManager = $this->app->get(ApplicationFileManager::class);
            $fileManager->writeFile($applicationStage, $bankStatementField, $bankStatementFileId, 'some-content');
        }

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->validateApplication($params);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals($encryptedResponseStatus, $encryptedResponse->status);
        $this->assertJson(json_encode($responseBody), $encryptedResponse->data);
    }

    /**
     * @return array[?string, EncryptedResponseStatus, array]
     */
    public static function validateEmailFieldDataProvider(): array
    {
        $faker = Faker::create();
        return [
            "valid email" => [
                $faker->freeEmail(),
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => [
                    ]
                ],
            ],
            "invalid email" => [
                'aa@bb.notexisting',
                EncryptedResponseStatus::UNPROCESSABLE_ENTITY,
                [
                    "validationResult" => [
                        "email" => [
                            [
                                "type" => "error",
                                "message" => "E-mailadres is geen geldig e-mailadres.",
                                "id" => null,
                                "params" => []
                            ]
                        ],
                    ]
                ],
            ],
            "email empty" => [
                '',
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => []
                ],
            ],
            "email null" => [
                null,
                EncryptedResponseStatus::OK,
                [
                    "validationResult" => []
                ],
            ]
        ];
    }

    /**
     * @dataProvider validateEmailFieldDataProvider
     * @group validation
     * @group validate-email
     */
    public function testValidateEmailField(
        ?string $email,
        EncryptedResponseStatus $encryptedResponseStatus,
        array $responseBody,
    ): void {
        $application = Application::factory()->for($this->identity)->for($this->subsidyVersion)->create();
        ApplicationStage::factory()->for($application)->for($this->subsidyStage1)->create();

        $emailField = Field::factory()
            ->for($this->subsidyStage1)
            ->create([
                'code' => 'email',
                'type' => FieldType::TextEmail,
            ]);

        $params = [
            $emailField->code => $email,
            'submit' => true,
        ];

        $body = new FieldValidationParams(
            (object)$params
        );

        $json = (new JSONEncoder())->encode($body);

        $params = new EncryptedApplicationValidationParams(
            new EncryptedIdentity(
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, '')
            ),
            $this->publicKey,
            $application->reference,
            new BinaryData($json) // NOTE: frontend encryption is disabled, so plain text
        );

        $encryptedResponse = $this->app->get(ApplicationMutationService::class)->validateApplication($params);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals($encryptedResponseStatus, $encryptedResponse->status);
        $this->assertJson(json_encode($responseBody), $encryptedResponse->data);
    }
}
