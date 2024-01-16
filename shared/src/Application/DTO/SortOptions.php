<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

readonly class SortOptions
{
    /**
     * @param SortColumn[] $sortColumns
     */
    public function __construct(
        protected array $sortColumns = [],
    ) {
    }

    /**
     * @return SortColumn[]
     */
    public function getSortColumns(): array
    {
        return $this->sortColumns;
    }

    public function hasSortColumns(): bool
    {
        return count($this->sortColumns) > 0;
    }

    public function containsColumn(string $columnName): bool
    {
        foreach ($this->sortColumns as $sortColumn) {
            if ($sortColumn->getColumn() === $columnName) {
                return true;
            }
        }
        return false;
    }

    public function append(SortColumn $sortColumn): SortOptions
    {
        $sortColumns = $this->sortColumns;
        $sortColumns[] = $sortColumn;
        return new SortOptions($sortColumns);
    }

    /**
     * @param string[] $columnNames Array of strings like ['column1', '-column2', 'column3']
     * @return SortOptions
     */
    public static function fromArray(array $columnNames): SortOptions
    {
        $sortColumns = [];
        foreach ($columnNames as $columnName) {
            $sortColumns[] = SortColumn::fromString($columnName);
        }
        return new SortOptions($sortColumns);
    }

    /**
     * @param ?string $columnNames String like 'column1,-column2,column3'
     * @return SortOptions
     */
    public static function fromString(?string $columnNames = null): SortOptions
    {
        if (empty($columnNames)) {
            return new SortOptions([]);
        }

        return self::fromArray(explode(',', $columnNames));
    }
}
