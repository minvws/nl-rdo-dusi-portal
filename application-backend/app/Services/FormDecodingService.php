<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use JsonException;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\Codable\Exceptions\PathNotFoundException;
use MinVWS\Codable\Exceptions\ValueNotFoundException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;

readonly class FormDecodingService
{
    public function __construct(private SubsidyRepository $subsidyRepository)
    {
    }

    /**
     * @throws PathNotFoundException|ValueNotFoundException|ValueTypeMismatchException|CodableException
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
     * @return array<int|string, FieldValue>
     * @throws PathNotFoundException|ValueNotFoundException|ValueTypeMismatchException|CodableException|JsonException
     */
    public function decodeFormValues(SubsidyStage $subsidyStage, string $data): array
    {
        $decoder = new JSONDecoder();
        $container = $decoder->decode($data);
        $values = [];
        $fields = $this->subsidyRepository->getFields($subsidyStage);
        foreach ($fields as $field) {
            $fieldContainer = $container->nestedContainer($field->code);
            $values[$field->code] = $this->decodeFieldValue($field, $fieldContainer);
        }
        return $values;
    }
}
