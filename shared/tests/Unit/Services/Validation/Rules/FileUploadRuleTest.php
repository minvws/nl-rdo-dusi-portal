<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Services\Validation\Rules;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\File;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\FileUploadRule;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class FileUploadRuleTest extends TestCase
{
    public function testUploadFieldNotRequiredAndValueNull(): void
    {
        $this->expectNotToPerformAssertions();

        $applicationStage = new ApplicationStage();
        $field = new Field([
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => false,
        ]);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $rule = new FileUploadRule($field);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', null, fn ($message) => $this->fail('Should not fail!'));
    }

    public function testUploadFieldNotRequiredAndValueNotNullAndFileExists(): void
    {
        $this->expectNotToPerformAssertions();

        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $fileUuid = Uuid::uuid4()->toString();

        $mockApplicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $mockApplicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(true);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($mockApplicationFileManager);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn ($message) => $this->fail('Should not fail!'));
    }

    public function testUploadFieldNotRequiredAndValueNotNullAndFileNotExists(): void
    {
        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $fileUuid = Uuid::uuid4()->toString();

        $mockApplicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $mockApplicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(false);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($mockApplicationFileManager);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn ($message) => $this->assertEquals('File not found!', $message));
    }

    public function testUploadFieldRequiredAndFileNotExists(): void
    {
        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $fileUuid = Uuid::uuid4()->toString();

        $mockApplicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $mockApplicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(false);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($mockApplicationFileManager);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn ($message) => $this->assertEquals('File not found!', $message));
    }

    public function testUploadFieldRequiredAndFileExists(): void
    {
        $this->expectNotToPerformAssertions();

        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $fileUuid = Uuid::uuid4()->toString();

        $mockApplicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $mockApplicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(true);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($mockApplicationFileManager);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn($message) => $this->fail('Should not fail!'));
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
