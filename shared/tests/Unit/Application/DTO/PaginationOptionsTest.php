<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Application\DTO;

use InvalidArgumentException;
use MinVWS\DUSi\Shared\Application\DTO\PaginationOptions;
use PHPUnit\Framework\TestCase;

/**
 * @group application
 * @group application-dto
 */
class PaginationOptionsTest extends TestCase
{
    public function testPaginationOptionsCanBeCreatedWithDefaultOptions(): void
    {
        $paginationOptions = new PaginationOptions();

        $this->assertInstanceOf(PaginationOptions::class, $paginationOptions);
        $this->assertSame(1, $paginationOptions->getPage());
        $this->assertSame(15, $paginationOptions->getPerPage());
    }

    public function testPaginationOptionsCanBeCreatedWithPage(): void
    {
        $paginationOptions = new PaginationOptions(page: 2);

        $this->assertInstanceOf(PaginationOptions::class, $paginationOptions);
        $this->assertSame(2, $paginationOptions->getPage());
        $this->assertSame(15, $paginationOptions->getPerPage());
    }

    public function testPaginationOptionsCanBeCreatedWithPerPage(): void
    {
        $paginationOptions = new PaginationOptions(perPage: 30);

        $this->assertInstanceOf(PaginationOptions::class, $paginationOptions);
        $this->assertSame(1, $paginationOptions->getPage());
        $this->assertSame(30, $paginationOptions->getPerPage());
    }

    public function testPaginationOptionsCanBeCreatedWithPageAndPerPage(): void
    {
        $paginationOptions = new PaginationOptions(page: 2, perPage: 30);

        $this->assertInstanceOf(PaginationOptions::class, $paginationOptions);
        $this->assertSame(2, $paginationOptions->getPage());
        $this->assertSame(30, $paginationOptions->getPerPage());
    }

    public function testPaginationOptionsCanBeCreatedFromArrayWithDefault(): void
    {
        $paginationOptions = PaginationOptions::fromArray([]);

        $this->assertInstanceOf(PaginationOptions::class, $paginationOptions);
        $this->assertSame(1, $paginationOptions->getPage());
        $this->assertSame(15, $paginationOptions->getPerPage());
    }

    public function testPaginationOptionsCanBeCreatedFromArrayWithPageAndPerPage(): void
    {
        $paginationOptions = PaginationOptions::fromArray(['page' => 2, 'per_page' => 20]);

        $this->assertInstanceOf(PaginationOptions::class, $paginationOptions);
        $this->assertSame(2, $paginationOptions->getPage());
        $this->assertSame(20, $paginationOptions->getPerPage());
    }

    public function testPaginationOptionsCanBeCreatedFromArrayWithPageAndPerPageAsString(): void
    {
        $paginationOptions = PaginationOptions::fromArray(['page' => '2', 'per_page' => '20']);

        $this->assertInstanceOf(PaginationOptions::class, $paginationOptions);
        $this->assertSame(2, $paginationOptions->getPage());
        $this->assertSame(20, $paginationOptions->getPerPage());
    }

    public function testPaginationOptionsCanBeCreatedFromArrayWithPageAndPerPageAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page parameter of PaginationOptions must be numeric');

        PaginationOptions::fromArray(['page' => null, 'per_page' => null]);
    }

    public function testPaginationOptionsCanBeCreatedFromArrayWithPageAndPerPageAsText(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page parameter of PaginationOptions must be numeric');

        PaginationOptions::fromArray(['page' => 'random', 'per_page' => 'random']);
    }
}
