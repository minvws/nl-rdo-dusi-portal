<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use InvalidArgumentException;
use MinVWS\DUSi\Shared\Application\Services\Validation\FileValidator;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class ApplicationFileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('save', $this->route('application')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(FileValidator $fileValidator): array
    {
        $field = $this->getField();

        return [
            'file' => $fileValidator->getRules($field),
        ];
    }

    protected function getField(): Field
    {
        $field = $this->route('field');
        if (! $field instanceof Field) {
            throw new InvalidArgumentException('Field not found');
        }

        return $field;
    }
}
