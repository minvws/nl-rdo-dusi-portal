<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use MinVWS\Codable\Decoding\Decoder;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Throwable;

readonly class FormDecodingService
{
    public function __construct(
        private SubsidyRepository $subsidyRepository,
        private Decoder $decoder,
    ) {
    }

    /**
     * @return array<int|string, FieldValue>
     * @throws Throwable
     */
    public function decodeFormValues(SubsidyStage $subsidyStage, object $data): array
    {
        $container = $this->decoder->decode($data);

        $values = [];
        $fields = $this->subsidyRepository->getFields($subsidyStage);

        foreach ($fields as $field) {
            $fieldContainer = $container->nestedContainer($field->code);
            $values[$field->code] = $this->decodeFieldValue($field, $fieldContainer);
        }
        return $values;
    }

    /**
     * @throws Throwable
     */
    private function decodeFieldValue(Field $field, DecodingContainer $container): FieldValue
    {
        $type = match ($field->type) {
            FieldType::TextNumeric => 'int',
            FieldType::TextFloat => 'float',
            FieldType::Checkbox => 'bool',
            FieldType::Upload => FileList::class,
            FieldType::Multiselect => 'array',
            default => 'string'
        };
        $value = $this->decodeFieldValueIfPresent($type, $container);
        return new FieldValue($field, $value);
    }

    /**
     * @param string|null $valueType
     * @param DecodingContainer $container
     * @return FileList|string|int|bool|float|array|null
     * @throws CodableException
     * @throws ValueTypeMismatchException
     */
    private function decodeFieldValueIfPresent(
        ?string $valueType,
        DecodingContainer $container
    ): FileList|string|int|bool|float|array|null {
        $value = $container->getRawValue();
        if (is_array($value) && $valueType === FileList::class) {
            $value = array_values(array_filter($value));

            $container = new DecodingContainer(
                value: $value,
                context: $container->getContext(),
                parent: $container->getParent(),
                key: $container->getKey(),
            );
        }

        if (is_array($value) && count($value) === 0) {
            return null;
        }

        if ($valueType === 'float' && is_int($value)) {
            $container = new DecodingContainer(
                value: (float)$value,
                context: $container->getContext(),
                parent: $container->getParent(),
                key: $container->getKey(),
            );
        }

        return $container->decodeIfPresent($valueType);
    }
}
