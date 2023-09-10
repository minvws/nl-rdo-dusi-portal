<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use JsonException;
use MinVWS\DUSi\Application\Backend\Services\FormDecodingService;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class FormDecodingServiceTest extends TestCase
{
    /**
     * @dataProvider dataProviderTestDecodeFormValues
     */
    public function testDecodeFormValues(FieldType $fieldType, string $jsonFormData, mixed $value): void
    {
        $subsidyStage = SubsidyStage::factory()->create();

        Field::factory()->for($subsidyStage)->create([
            'code' => 'code',
            'type' => $fieldType->value,
            'is_required' => true,
        ]);

        $repository = app(SubsidyRepository::class);

        $decodingService = new FormDecodingService($repository);
        $values = $decodingService->decodeFormValues(
            $subsidyStage,
            $jsonFormData,
        );

        $this->assertCount(1, $values);
        $this->assertArrayHasKey('code', $values);
        $this->assertSame($value, $values['code']->value);
    }

    /**
     * @throws JsonException
     */
    public static function dataProviderTestDecodeFormValues(): array
    {
        return [
            'field type text' => self::getTestDataForField(FieldType::Text, 'tekst'),
            'field type text numeric' => self::getTestDataForField(FieldType::TextNumeric, 1),
            'field type text email' => self::getTestDataForField(FieldType::TextEmail, 'tekst'),
            'field type text tel' => self::getTestDataForField(FieldType::TextTel, '+31123456'),
            'field type text url' => self::getTestDataForField(FieldType::TextTel, 'https://rdobeheer.nl'),
            'field type checkbox true' => self::getTestDataForField(FieldType::Checkbox, true),
            'field type checkbox false' => self::getTestDataForField(FieldType::Checkbox, false),
            'field type multiselect' => self::getTestDataForField(FieldType::Multiselect, ['a', 'b']),
            'field type select' => self::getTestDataForField(FieldType::Select, 'a'),
            'field type textarea' => self::getTestDataForField(FieldType::TextArea, 'b'),
            'field type upload' => self::getTestDataForField(FieldType::Upload, 'c'),
            'field type date' => self::getTestDataForField(FieldType::Date, '2023-12-31'),
            'field type custom postal code' => self::getTestDataForField(FieldType::CustomPostalCode, '1234AB'),
            'field type custom country' => self::getTestDataForField(FieldType::CustomCountry, 'NL'),
            'field type custom bank account' =>
                self::getTestDataForField(FieldType::CustomBankAccount, 'NL18RABO0123459876'),
        ];
    }

    /**
     * @throws JsonException
     */
    protected static function getTestDataForField(FieldType $fieldType, mixed $value): array
    {
        $form = [
            'code' => $value,
        ];

        return [
            $fieldType,
            json_encode($form, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
            $value,
        ];
    }
}
