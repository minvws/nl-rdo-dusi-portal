<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Submission;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class FormData implements Codable
{
    /**
     * @param array<FieldValue> $fieldValues
     */
    public function __construct(
        public readonly array $fieldValues
    ) {
    }

    /**
     * @throws Throwable
     */
    private static function decodeFieldValue(Field $field, DecodingContainer $container): FieldValue
    {
        $type = match ($field->type) {
            FieldType::TextNumeric => 'int',
            FieldType::Checkbox => 'bool',
            FieldType::Upload => FileList::class,
            default => 'string'
        };

        $value = $container->decodeIfPresent($type);
        return new FieldValue($field, $value);
    }

    public static function decode(DecodingContainer $container, ?Decodable $object = null): Decodable
    {
    }

    public function encode(EncodingContainer $container): void
    {
    }
}