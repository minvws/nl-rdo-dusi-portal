<?php
declare(strict_types=1);

namespace App\Services;

use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\FieldType;
use App\Shared\Models\Definition\Form;
use App\Models\Submission\FieldValue;
use App\Repositories\FormRepository;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\JSON\JSONDecoder;
use Throwable;

readonly class FormDecodingService
{
    public function __construct(private FormRepository $formRepository)
    {
    }

    /**
     * @throws Throwable
     */
    private function decodeFieldValue(Field $field, DecodingContainer $container): FieldValue
    {
        $type = match ($field->type) {
            FieldType::TextNumeric => 'int',
            FieldType::Checkbox => 'bool',
            default => 'string'
        };

        if ($field->is_required) {
            $value = $container->decode($type);
        } else {
            $value = $container->decodeIfExists($type);
        }

        return new FieldValue($field, $value);
    }

    /**
     * @return array<string, FieldValue>
     * @throws Throwable
     */
    public function decodeFormValues(Form $form, string $data): array
    {
        $decoder = new JSONDecoder();
        $container = $decoder->decode($data);

        $values = [];
        $fields = $this->formRepository->getFields($form);
        foreach ($fields as $field) {
            $fieldContainer = $container->nestedContainer($field->code);
            $values[$field->code] = $this->decodeFieldValue($field, $fieldContainer);
        }

        return $values;
    }
}
