<?php
declare(strict_types=1);

namespace App\Services;

use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\FieldType;
use App\Shared\Models\Definition\Form;
use App\Models\Submission\FieldValue;
use App\Models\Submission\FormSubmit;
use App\Repositories\FormRepository;
use Exception;
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
    private function getForm(string $formId): Form
    {
        $form = $this->formRepository->getForm($formId);
        if ($form === null) {
            throw new Exception('Form not found!');
        }

        return $form;
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
     * @throws Throwable
     */
    public function decodeFormSubmit(string $formId, string $data): FormSubmit
    {
        $form = $this->getForm($formId);

        $decoder = new JSONDecoder();
        $container = $decoder->decode($data);

        $values = [];
        foreach ($form->fields as $field) {
            $fieldContainer = $container->nestedContainer($field->code);
            $values[$field->code] = $this->decodeFieldValue($field, $fieldContainer);
        }

        return new FormSubmit($form, $values);
    }
}
