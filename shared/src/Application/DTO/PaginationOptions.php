<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use InvalidArgumentException;

readonly class PaginationOptions
{
    protected const DEFAULT_PAGE = 1;
    protected const DEFAULT_PER_PAGE = 15;

    public function __construct(
        protected int $page = self::DEFAULT_PAGE,
        protected int $perPage = self::DEFAULT_PER_PAGE,
    ) {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param array{page?: int|string, per_page?: int|string} $inputArray
     * @return PaginationOptions
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $inputArray): PaginationOptions
    {
        if (array_key_exists('page', $inputArray) && !is_numeric($inputArray['page'])) {
            throw new InvalidArgumentException('Page parameter of PaginationOptions must be numeric');
        }

        if (array_key_exists('per_page', $inputArray) && !is_numeric($inputArray['per_page'])) {
            throw new InvalidArgumentException('Per page parameter of PaginationOptions must be numeric');
        }

        return new PaginationOptions(
            page: (int) ($inputArray['page'] ?? self::DEFAULT_PAGE),
            perPage: (int) ($inputArray['per_page'] ?? self::DEFAULT_PER_PAGE),
        );
    }
}
