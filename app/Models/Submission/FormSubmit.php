<?php
declare(strict_types=1);

namespace App\Models\Submission;

use App\Shared\Models\Definition\Form;

readonly class FormSubmit
{
    /**
     * @param array<FieldValue> $values
     */
    public function __construct(
        public Form $form,
        public array $values
    ) {
    }
}
