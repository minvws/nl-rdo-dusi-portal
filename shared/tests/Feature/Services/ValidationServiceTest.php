<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Services;

use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\SurePayRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ValidationErrorException;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidatorFactory;
use MinVWS\DUSi\Shared\Application\Services\ValidationService;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Tests\TestCase;
use Mockery;

/**
 * @group validation
 */
class ValidationServiceTest extends TestCase
{
    use WithFaker;

    /**
     * @dataProvider dataProviderTestFieldRules
     */
    public function testRules(
        FieldType $fieldType,
        array $fieldParams,
        string|int|bool|float|FileList|array|null $value,
        bool $passes
    ): void {
        if (!$passes) {
            $this->expectException(ValidationErrorException::class);
        }

        $application = Application::factory()->create();
        $applicationStage = ApplicationStage::factory()->create([
            'application_id' => $application->id,
        ]);

        $applicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $applicationRepository = Mockery::mock(ApplicationRepository::class);
        $bankAccountRepository = Mockery::mock(SurePayRepository::class);

        $factory = new ValidatorFactory(
            applicationFileManager: $applicationFileManager,
            applicationRepository: $applicationRepository,
            translator: app('translator'),
        );

        $validationService = new ValidationService(
            validatorFactory: $factory,
            bankAccountRepository: $bankAccountRepository,
            translator: app('translator'),
        );

        $fieldValue = new FieldValue(
            Field::factory()
                ->for($applicationStage->subsidyStage)
                ->create([
                    'code' => 'code1',
                    'type' => $fieldType->value,
                    'is_required' => true,
                    'params' => $fieldParams,
                ]),
            value: $value,
        );

        $validator = $validationService->getValidator(
            applicationStage: $applicationStage,
            fieldValues: [$fieldValue],
            submit: true,
        );

        $validationResults = $validator->validate();
        $this->assertCount(0, $validationResults);
    }

    public static function dataProviderTestFieldRules(): array
    {
        return [
            'test text field with valid max length' => [
                FieldType::Text,
                [
                    'maxLength' => 10,
                ],
                'ABCDEFGHIJ',
                true,
            ],
            'test text field with invalid max length' => [
                FieldType::Text,
                [
                    'maxLength' => 10,
                ],
                'ABCDEFGHIJK',
                false,
            ],
            'test text field with valid min length' => [
                FieldType::Text,
                [
                    'minLength' => 10,
                ],
                'ABCDEFGHIJ',
                true,
            ],
            'test text field with invalid min length' => [
                FieldType::Text,
                [
                    'minLength' => 10,
                ],
                'ABCDEFGHI',
                false,
            ],
            'test text field with valid min and max length' => [
                FieldType::Text,
                [
                    'minLength' => 10,
                    'maxLength' => 10,
                ],
                'ABCDEFGHIJ',
                true,
            ],
            'test text field with invalid min and max length' => [
                FieldType::Text,
                [
                    'minLength' => 10,
                    'maxLength' => 10,
                ],
                'ABCDEFGHI',
                false,
            ],
            'test text field with valid min and max length in between' => [
                FieldType::Text,
                [
                    'minLength' => 5,
                    'maxLength' => 15,
                ],
                'ABCDEFGHIJKL',
                true,
            ],
            'test numeric field with valid minimum 10 and value 10' => [
                FieldType::TextNumeric,
                [
                    'minimum' => 10,
                ],
                10,
                true,
            ],
            'test numeric field with valid minimum 10 and value 11' => [
                FieldType::TextNumeric,
                [
                    'minimum' => 10,
                ],
                11,
                true,
            ],
            'test numeric field with invalid minimum 10 and value 9' => [
                FieldType::TextNumeric,
                [
                    'minimum' => 10,
                ],
                9,
                false,
            ],
            'test numeric field with valid maximum 10 and value 10' => [
                FieldType::TextNumeric,
                [
                    'maximum' => 10,
                ],
                10,
                true,
            ],
            'test numeric field with valid maximum 10 and value 9' => [
                FieldType::TextNumeric,
                [
                    'maximum' => 10,
                ],
                9,
                true,
            ],
            'test numeric field with invalid minimum 10 and value 11' => [
                FieldType::TextNumeric,
                [
                    'maximum' => 10,
                ],
                11,
                false,
            ],
            'test numeric field with valid minimum and maximum value' => [
                FieldType::TextNumeric,
                [
                    'minimum' => 10,
                    'maximum' => 10,
                ],
                10,
                true,
            ],
            'test numeric field with invalid minimum and maximum value' => [
                FieldType::TextNumeric,
                [
                    'minimum' => 10,
                    'maximum' => 10,
                ],
                11,
                false,
            ],
            'test numeric field with valid minimum and maximum value in between' => [
                FieldType::TextNumeric,
                [
                    'minimum' => 5,
                    'maximum' => 15,
                ],
                8,
                true,
            ],
            'test required checkbox field value true' => [
                FieldType::Checkbox,
                [],
                true,
                true,
            ],
            'test required checkbox field value false' => [
                FieldType::Checkbox,
                [],
                false,
                false,
            ],
            'test select field value in array' => [
                FieldType::Select,
                [
                    'options' => [
                        'A',
                        'B',
                        'C',
                    ],
                ],
                'A',
                true,
            ],
            'test select field value not in array' => [
                FieldType::Select,
                [
                    'options' => [
                        'A',
                        'B',
                        'C',
                    ],
                ],
                'D',
                false,
            ],
            'test multiselect field value in array' => [
                FieldType::Multiselect,
                [
                    'options' => [
                        'A',
                        'B',
                        'C',
                    ],
                ],
                ['A'],
                true,
            ],
            'test multiselect field multiple values in array' => [
                FieldType::Multiselect,
                [
                    'options' => [
                        'A',
                        'B',
                        'C',
                    ],
                ],
                ['A', 'B'],
                true,
            ],
            'test multiselect field value not in array' => [
                FieldType::Multiselect,
                [
                    'options' => [
                        'A',
                        'B',
                        'C',
                    ],
                ],
                ['D'],
                false,
            ],
        ];
    }


    /**
     * @group validation-required-condition
     * @dataProvider requiredConditionRuleProvider
     */
    public function testRequiredConditionRule(
        ?string $value1,
        ?string $comparisonValue,
        ?string $value2,
        bool $passes
    ): void {
        if (!$passes) {
            $this->expectException(ValidationErrorException::class);
        }

        $application = Application::factory()->create();
        $applicationStage = ApplicationStage::factory()->create([
            'application_id' => $application->id,
        ]);

        $applicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $applicationRepository = Mockery::mock(ApplicationRepository::class);
        $bankAccountRepository = Mockery::mock(SurePayRepository::class);

        $factory = new ValidatorFactory(
            applicationFileManager: $applicationFileManager,
            applicationRepository: $applicationRepository,
            translator: app('translator'),
        );

        $validationService = new ValidationService(
            validatorFactory: $factory,
            bankAccountRepository: $bankAccountRepository,
            translator: app('translator'),
        );

        $fieldValue1 = new FieldValue(
            Field::factory()
                ->for($applicationStage->subsidyStage)
                ->create([
                    'code' => 'code1',
                    'type' => FieldType::Text,
                    'is_required' => true,
                    'params' => [],
                ]),
            value: $value1,
        );

        $condition =
            new ComparisonCondition(1, 'code1', Operator::Identical, $comparisonValue);

        $fieldValue2 = new FieldValue(
            Field::factory()
                ->for($applicationStage->subsidyStage)
                ->create([
                    'code' => 'code2',
                    'type' => FieldType::Text,
                    'is_required' => false,
                    'required_condition' => $condition,
                    'params' => [],
                ]),
            value: $value2,
        );

        $validator = $validationService->getValidator(
            applicationStage: $applicationStage,
            fieldValues: [$fieldValue1, $fieldValue2],
            submit: true,
        );

        $validationResult = $validator->validate();
        $this->assertCount(0, $validationResult);
    }

    public static function requiredConditionRuleProvider(): array
    {
        return [
            'condition is matched and value is set' => ['a', 'a', 'value', true],
            'condition is matched and value is not set' => ['a', 'a', null, false],
            'condition is matched and value is empty string' => ['a', 'a', '', false],
            'condition is not matched and value is set' => ['a', 'b', 'value', true],
            'condition is not matched and value is not set' => ['a', 'b', null, true],
            'condition is not matched and value is empty string' => ['a', 'b', '', true],
        ];
    }
}
