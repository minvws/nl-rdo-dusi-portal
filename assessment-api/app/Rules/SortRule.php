<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SortRule implements ValidationRule
{
    /**
     * Explode the sort query parameter into an array of columns.
     * Check if the columns are in the sortable columns array.
     *
     * @param array<string> $sortableColumns
     * @param string $descendingSortPrefix
     */
    public function __construct(protected array $sortableColumns, protected string $descendingSortPrefix = '-')
    {
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) $attribute is not used
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail("The sort parameter must be a string.");
        }

        if ($value === '') {
            return;
        }

        $columns = explode(',', $value);
        foreach ($columns as $column) {
            $column = ltrim($column, $this->descendingSortPrefix);

            if (!in_array($column, $this->sortableColumns, true)) {
                $fail("The sort column '$column' is not sortable.");
            }
        }
    }
}
