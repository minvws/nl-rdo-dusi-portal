<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\Validation\ValidatorFactory;
use MinVWS\DUSi\Application\Backend\Services\ValidationService;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Mockery;

class ValidationServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function testSelectFieldRules(): void
    {
//        $this->markTestSkipped('TODO: implement testSelectFieldRules()');
//
//        // TODO: Write tests for select and multi select ... with dataprovider
//
//        $application = Application::factory()->create();
//        $applicationStage = ApplicationStage::factory()->create([
//            'application_id' => $application->id,
//        ]);
//
//        $fileRepository = Mockery::mock(ApplicationFileRepository::class);
//        $applicationRepository = Mockery::mock(ApplicationRepository::class);
//
//        $factory = new ValidatorFactory(
//            fileRepository: $fileRepository,
//            applicationRepository: $applicationRepository,
//        );
//
//        $validationService = new ValidationService(
//            validatorFactory: $factory,
//        );
//
//        $validator = $validationService->getValidator(
//            applicationStage: $applicationStage,
//            fieldValues: [
//                new FieldValue(
//                    new Field([
//                        'code' => 'code1',
//                        'type' => 'select',
//                        'is_required' => true,
//                        'options' => [
//                            'value1' => 'label1',
//                            'value2' => 'label2',
//                        ],
//                    ]),
//                    value: 'value1',
//                ),
//            ],
//        );
//
//        $this->assertEquals($validator->passes(), true);
    }

    /**
     * @dataProvider dataProviderTestFieldRules
     */
    public function testRules(
        FieldType $fieldType,
        array $fieldParams,
        string|int|bool|float|null $value,
        bool $passes
    ): void {
        $application = Application::factory()->create();
        $applicationStage = ApplicationStage::factory()->create([
            'application_id' => $application->id,
        ]);

        $fileRepository = Mockery::mock(ApplicationFileRepository::class);
        $applicationRepository = Mockery::mock(ApplicationRepository::class);

        $factory = new ValidatorFactory(
            fileRepository: $fileRepository,
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

//        dump($validator->passes(), $validator->failed());

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
//            'test numeric field with valid minimum 10 and value 10' => [
//                FieldType::TextNumeric,
//                [
//                    'minimum' => 10,
//                ],
//                10,
//                true,
//            ],
//            'test numeric field with valid minimum 10 and value 11' => [
//                FieldType::TextNumeric,
//                [
//                    'minimum' => 10,
//                ],
//                11,
//                true,
//            ],
//            'test numeric field with invalid minimum 10 and value 9' => [
//                FieldType::TextNumeric,
//                [
//                    'minimum' => 10,
//                ],
//                9,
//                false,
//            ],
        ];
    }
}
