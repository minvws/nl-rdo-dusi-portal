<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services\Validation\Rules;

use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\Validation\Rules\FileUploadRule;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class FileUploadRuleTest extends TestCase
{
    public function testUploadFieldNotRequiredAndAnswerNull(): void
    {
        $this->expectNotToPerformAssertions();

        $answer = null;
        $applicationStage = new ApplicationStage();
        $field = new Field([
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => false,
        ]);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository
            ->shouldReceive('getAnswer')
            ->withArgs([$applicationStage, $field])
            ->andReturn($answer);


        $rule = new FileUploadRule($field);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', null, fn ($message) => $this->fail('Should not fail!'));
    }

    public function testUploadFieldRequiredAndAnswerNull(): void
    {
        $answer = null;
        $applicationStage = new ApplicationStage();
        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository
            ->shouldReceive('getAnswer')
            ->withArgs([$applicationStage, $field])
            ->andReturn($answer);

        $rule = new FileUploadRule($field);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', null, fn ($message) => $this->assertEquals('Field is required!', $message));
    }

    public function testUploadFieldRequiredAndFileNotExists(): void
    {
        $applicationStageUuid = Uuid::uuid4();

        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $mockApplicationFileRepository = Mockery::mock(ApplicationFileRepository::class);
        $mockApplicationFileRepository->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field])
            ->andReturn(false);

        $answer = new Answer();
        $answer->application_stage_id = $applicationStageUuid;
        $answer->field_id = $field->id;
        $answer->encrypted_answer = 'some value';

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository->shouldReceive('getAnswer')
            ->withArgs([$applicationStage, $field])
            ->andReturn($answer);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileRepository($mockApplicationFileRepository);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', null, fn ($message) => $this->assertEquals('File not found!', $message));
    }

    public function testUploadFieldRequiredAndFileExists(): void
    {
        $this->expectNotToPerformAssertions();

        $applicationStageUuid = Uuid::uuid4();

        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $mockApplicationFileRepository = Mockery::mock(ApplicationFileRepository::class);
        $mockApplicationFileRepository->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field])
            ->andReturn(true);

        $answer = new Answer();
        $answer->application_stage_id = $applicationStageUuid;
        $answer->field_id = $field->id;
        $answer->encrypted_answer = 'some value';

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository->shouldReceive('getAnswer')
            ->withArgs([$applicationStage, $field])
            ->andReturn($answer);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileRepository($mockApplicationFileRepository);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', null, fn($message) => $this->fail('Should not fail!'));
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
