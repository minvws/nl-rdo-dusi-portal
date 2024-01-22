<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

readonly class SortColumn
{
    public function __construct(
        protected string $column,
        protected bool $ascending
    ) {
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function isAscending(): bool
    {
        return $this->ascending;
    }

    public function isDescending(): bool
    {
        return !$this->ascending;
    }

    public function getDirection(): string
    {
        return $this->isAscending() ? 'asc' : 'desc';
    }

    public static function fromString(string $inputString): SortColumn
    {
        $ascending = true;
        if (str_starts_with($inputString, '-')) {
            $ascending = false;
            $inputString = substr($inputString, 1);
        }
        return new SortColumn($inputString, $ascending);
    }
}
