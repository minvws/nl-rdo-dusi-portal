<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Services;

use MinVWS\Codable\Decoding\Decoder;
use MinVWS\DUSi\Shared\Application\Models\Submission\File;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Services\FormDecodingService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\Tests\TestCase;

class FormDecodingServiceTest extends TestCase
{
    /**
     * @dataProvider dataProviderTestDecodeFormValues
     */
    public function testDecodeFormValues(FieldType $fieldType, object $data, mixed $value, mixed $expectedValue): void
    {
        $subsidyStage = SubsidyStage::factory()->create();

        Field::factory()->for($subsidyStage)->create([
            'code' => 'code',
            'type' => $fieldType->value,
            'is_required' => true,
        ]);

        $repository = app(SubsidyRepository::class);
        $decoder = app(Decoder::class);

        $decodingService = new FormDecodingService($repository, $decoder);
        $values = $decodingService->decodeFormValues(
            $subsidyStage,
            $data,
        );

        $this->assertCount(1, $values);
        $this->assertArrayHasKey('code', $values);

        if ($fieldType === FieldType::Upload) {
            $this->assertEquals($expectedValue, $values['code']->value);
            return;
        }

        $this->assertSame($expectedValue, $values['code']->value);
    }

    public static function dataProviderTestDecodeFormValues(): array
    {
        return [
            'field type text' => self::getTestDataForFieldExpectSameValue(FieldType::Text, 'tekst'),
            'field type text numeric' => self::getTestDataForFieldExpectSameValue(FieldType::TextNumeric, 1),
            'field type text email' => self::getTestDataForFieldExpectSameValue(FieldType::TextEmail, 'tekst'),
            'field type text tel' => self::getTestDataForFieldExpectSameValue(FieldType::TextTel, '+31123456'),
            'field type text url' => self::getTestDataForFieldExpectSameValue(
                FieldType::TextTel,
                'https://rdobeheer.nl'
            ),
            'field type checkbox true' => self::getTestDataForFieldExpectSameValue(FieldType::Checkbox, true),
            'field type checkbox false' => self::getTestDataForFieldExpectSameValue(FieldType::Checkbox, false),
            'field type multiselect' => self::getTestDataForFieldExpectSameValue(FieldType::Multiselect, ['a', 'b']),
            'field type multiselect with empty list' => self::getTestDataForField(FieldType::Multiselect, [], null),
            'field type select' => self::getTestDataForFieldExpectSameValue(FieldType::Select, 'a'),
            'field type textarea' => self::getTestDataForFieldExpectSameValue(FieldType::TextArea, 'b'),
            'field type upload' => self::getTestDataForField(FieldType::Upload, [
                [
                    'id' => '225654e6-1db3-445c-8ff8-48679dd802c2',
                    'name' => 'file1.pdf',
                    'mimeType' => 'application/pdf'
                ],
            ], new FileList([
                new File('225654e6-1db3-445c-8ff8-48679dd802c2', 'file1.pdf', 'application/pdf'),
            ])),
            'field type upload with empty items' => self::getTestDataForField(FieldType::Upload, [
                null,
                [
                    'id' => '225654e6-1db3-445c-8ff8-48679dd802c2',
                    'name' => 'file1.pdf',
                    'mimeType' => 'application/pdf'
                ],
                null,
            ], new FileList([
                new File('225654e6-1db3-445c-8ff8-48679dd802c2', 'file1.pdf', 'application/pdf'),
            ])),
            'field type upload with only empty items' => self::getTestDataForField(FieldType::Upload, [
                null,
                null,
            ], null),
            'field type upload with empty list' => self::getTestDataForField(FieldType::Upload, [], null),
            'field type date' => self::getTestDataForFieldExpectSameValue(FieldType::Date, '2023-12-31'),
            'field type custom postal code' => self::getTestDataForFieldExpectSameValue(
                FieldType::CustomPostalCode,
                '1234AB'
            ),
            'field type custom country' => self::getTestDataForFieldExpectSameValue(FieldType::CustomCountry, 'NL'),
            'field type custom bank account' =>
                self::getTestDataForFieldExpectSameValue(FieldType::CustomBankAccount, 'NL18RABO0123459876'),
        ];
    }

    protected static function getTestDataForFieldExpectSameValue(FieldType $fieldType, mixed $value): array
    {
        return self::getTestDataForField($fieldType, $value, $value);
    }

    protected static function getTestDataForField(
        FieldType $fieldType,
        mixed $value,
        mixed $expectedValue,
    ): array {
        $form = [
            'code' => $value,
        ];

        return [
            $fieldType,
            (object) $form,
            $value,
            $expectedValue,
        ];
    }
}
