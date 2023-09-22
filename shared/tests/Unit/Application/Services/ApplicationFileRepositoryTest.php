<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Application\Services;

use Illuminate\Contracts\Encryption\StringEncrypter;
use Illuminate\Contracts\Filesystem\Filesystem;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationFileEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileRepositoryService;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group application
 * @group application-file-repository
 */
class ApplicationFileRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ApplicationFileEncryptionService $applicationFileEncryptionService;

    protected function setUp(): void
    {
        parent::setUp();

        $stringEncrypterMock = Mockery::mock(StringEncrypter::class);
        $stringEncrypterMock->shouldReceive('encryptString')
            ->andReturnUsing(fn ($value) => $value);
        $stringEncrypterMock->shouldReceive('decryptString')
            ->andReturnUsing(fn ($value) => $value);

        $this->applicationFileEncryptionService = Mockery::mock(ApplicationFileEncryptionService::class);
        $this->applicationFileEncryptionService->shouldReceive('generateKeyInfo')
            ->andReturn(['{}', $stringEncrypterMock]);
    }

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

        $id = '3';

        $fileSystem = Mockery::mock(Filesystem::class);
        $fileSystem->expects('put')
            ->with("1/2/3", 'contents')
            ->andReturn(true);
        $fileSystem->expects('put')
            ->with("1/2/3.keyinfo", '{}')
            ->andReturn(true);

        $repository = new ApplicationFileRepositoryService(
            $this->applicationFileEncryptionService,
            new ApplicationFileRepository($fileSystem)
        );
        $result = $repository->writeFile($applicationStage, $field, $id, 'contents');

        $this->assertTrue($result);
    }

    public function testRepositoryCanCallExists(): void
    {
        $applicationStage = new ApplicationStage();
        $applicationStage->id = '2';

        $field = new Field();
        $field->code = '3';

        $id = '4';

        $fileSystem = Mockery::mock(Filesystem::class);
        $fileSystem->expects('exists')
            ->with("2/3/4")
            ->andReturn(true);
        $fileSystem->expects('exists')
            ->with("2/3/4.keyinfo")
            ->andReturn(true);

        $repository = new ApplicationFileRepositoryService(
            $this->applicationFileEncryptionService,
            new ApplicationFileRepository($fileSystem)
        );
        $result = $repository->fileExists($applicationStage, $field, $id);

        $this->assertTrue($result);
    }
}
