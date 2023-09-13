<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use PHPUnit\Framework\TestCase;

class ApplicationFileRepositoryTest extends TestCase
{
    public function testRepositoryCanBeCreated(): void
    {
        $fileSystem = $this->createMock(Filesystem::class);

        $repository = new ApplicationFileRepository($fileSystem);

        $this->assertInstanceOf(ApplicationFileRepository::class, $repository);
    }

    public function testRepositoryCanCallWriteFile(): void
    {
        $applicationStage = new ApplicationStage();
        $applicationStage->id = '1';

        $field = new Field();
        $field->code = '2';

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem->expects($this->once())
            ->method('put')
            ->with("1/2", 'contents')
            ->willReturn(true);

        $repository = new ApplicationFileRepository($fileSystem);
        $result = $repository->writeFile($applicationStage, $field, 'contents');

        $this->assertTrue($result);
    }

    public function testRepositoryCanCallExists(): void
    {
        $applicationStage = new ApplicationStage();
        $applicationStage->id = '2';

        $field = new Field();
        $field->code = '3';

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem->expects($this->once())
            ->method('exists')
            ->with("2/3")
            ->willReturn(true);

        $repository = new ApplicationFileRepository($fileSystem);
        $result = $repository->fileExists($applicationStage, $field, 'contents');

        $this->assertTrue($result);
    }
}
