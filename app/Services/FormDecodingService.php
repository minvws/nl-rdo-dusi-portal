<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Submission\FieldValue;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class FormDecodingService
{
    public function __construct(private SubsidyRepository $subsidyRepository)
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
     * @return array<int|string, FieldValue>
     * @throws Throwable
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
