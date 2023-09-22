<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Rules;

use MinVWS\DUSi\Shared\Application\DTO\TemporaryFile;
use MinVWS\DUSi\Shared\Application\Services\Clamav\ClamAvService;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\ClamAv;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Xenolope\Quahog\Client;
use Xenolope\Quahog\Result;

class ClamAvRuleTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testEnabled(): void
    {
        $clamAvService = Mockery::mock(ClamAvService::class);
        $clamAvService->expects('enabled')
            ->andReturn(false);

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('debug')
            ->with('Skipping ClamAV scan because skipValidation is set to true');

        $rule = new ClamAv($clamAvService, $logger);
        $rule->validate('test', null, function ($message) {
            $this->fail('Fail should not be called.');
        });
    }

    public function testValidateValueCheck(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);

        $clamAvService = Mockery::mock(ClamAvService::class);
        $clamAvService->expects('enabled')
            ->andReturn(true);

        $rule = new ClamAv($clamAvService, $logger);
        $rule->validate('test', null, function ($message) {
            $this->assertSame('The :attribute must be an uploaded file.', $message);
        });
    }

    public function testValidateUploadedFileOk(): void
    {
        $tempFile = new TemporaryFile(file_get_contents(__DIR__ . '/../../fixtures/test.pdf'));
        $uploadedFile = $tempFile->getUploadedFile();

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('debug')
            ->with('ClamAV scan succeeded');

        $resultMock = Mockery::mock(Result::class);
        $resultMock->expects('isOk')
            ->andReturn(true);

        $clientMock = Mockery::mock(Client::class);
        $clientMock->expects('scanFile')
            ->with($uploadedFile->getPathname())
            ->andReturn($resultMock);

        $clamAvService = Mockery::mock(ClamAvService::class);
        $clamAvService->expects('enabled')
            ->andReturn(true);
        $clamAvService->expects('getClamAvClient')
            ->andReturn($clientMock);

        $rule = new ClamAv($clamAvService, $logger);
        $rule->validate('test', $uploadedFile, function ($message) {
            $this->fail('Fail should not be called.');
        });

        $tempFile->close();
    }

    public function testValidateUploadedFileNotOk(): void
    {
        $tempFile = new TemporaryFile(file_get_contents(__DIR__ . '/../../fixtures/test.pdf'));
        $uploadedFile = $tempFile->getUploadedFile();

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('info')
            ->with('ClamAV scan failed', [
                'failed' => true,
                'error' => false,
                'found' => true,
                'reason' => 'A virus found',
            ]);

        $resultMock = Mockery::mock(Result::class);
        $resultMock->expects('isOk')
            ->andReturn(false);
        $resultMock->expects('hasFailed')
            ->andReturn(true);
        $resultMock->expects('isError')
            ->andReturn(false);
        $resultMock->expects('isFound')
            ->andReturn(true);
        $resultMock->expects('getReason')
            ->andReturn('A virus found');

        $clientMock = Mockery::mock(Client::class);
        $clientMock->expects('scanFile')
            ->with($uploadedFile->getPathname())
            ->andReturn($resultMock);

        $clamAvService = Mockery::mock(ClamAvService::class);
        $clamAvService->expects('enabled')
            ->andReturn(true);
        $clamAvService->expects('getClamAvClient')
            ->andReturn($clientMock);

        $rule = new ClamAv($clamAvService, $logger);
        $rule->validate('test', $uploadedFile, function ($message) {
            $this->assertSame('The :attribute is not a valid file.', $message);
        });

        $tempFile->close();
    }
}
