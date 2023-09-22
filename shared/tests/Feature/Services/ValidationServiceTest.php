<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidatorFactory;
use MinVWS\DUSi\Shared\Application\Services\ValidationService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Tests\TestCase;
use Mockery;

class ValidationServiceTest extends TestCase
{
    use DatabaseTransactions;
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
        $application = Application::factory()->create();
        $applicationStage = ApplicationStage::factory()->create([
            'application_id' => $application->id,
        ]);

        $applicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $applicationRepository = Mockery::mock(ApplicationRepository::class);

        $factory = new ValidatorFactory(
            applicationFileManager: $applicationFileManager,
            applicationRepository: $applicationRepository,
        );

        $validationService = new ValidationService(
            validatorFactory: $factory,
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
            fieldValues: [
                $fieldValue,
            ],
        );

        $this->assertEquals($passes, $validator->passes());
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
}
