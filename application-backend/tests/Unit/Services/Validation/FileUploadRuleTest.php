<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services\Validation;

use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\Validation\Rules\FileUploadRule;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
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

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository
            ->shouldReceive('getAnswer')
            ->andReturn($answer);

        $applicationStageVersion = new ApplicationStageVersion([
            'id' => Uuid::uuid4(),
            'application_id' => Uuid::uuid4(),
            'application_stage_id' => Uuid::uuid4(),
            'application_stage' => new ApplicationStage([
                'id' => Uuid::uuid4(),
                'application_form_id' => Uuid::uuid4()
            ]),
        ]);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => false,
        ]);

        $rule = new FileUploadRule($field);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStageVersion($applicationStageVersion);

        $rule->validate('uploadField', null, fn ($message) => $this->fail('Should not fail!'));
    }

    public function testUploadFieldRequiredAndAnswerNull(): void
    {
        $answer = null;

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository
            ->shouldReceive('getAnswer')
            ->andReturn($answer);

        $applicationStageVersion = new ApplicationStageVersion([
            'id' => Uuid::uuid4(),
            'application_id' => Uuid::uuid4(),
            'application_stage_id' => Uuid::uuid4(),
            'application_stage' => new ApplicationStage([
                'id' => Uuid::uuid4(),
                'application_form_id' => Uuid::uuid4()
            ]),
        ]);

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $rule = new FileUploadRule($field);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStageVersion($applicationStageVersion);

        $rule->validate('uploadField', null, fn ($message) => $this->assertEquals('Field is required!', $message));
    }

    public function testUploadFieldRequiredAndFileNotExists(): void
    {
        $applicationStageVersionUuid = Uuid::uuid4();

        $applicationStageVersion = Mockery::mock(ApplicationStageVersion::class);

        $applicationStageVersion
            ->expects('getAttribute')
            ->with('id')
            ->andReturn($applicationStageVersionUuid);
        $applicationStageVersion
            ->expects('getAttribute')
            ->with('applicationStage')
            ->andReturn(new ApplicationStage([
                'id' => $applicationStageVersionUuid,
                'application_form_id' => Uuid::uuid4()
            ]));

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $mockApplicationFileRepository = Mockery::mock(ApplicationFileRepository::class);
        $mockApplicationFileRepository->shouldReceive('fileExists')
            ->andReturn(false);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository->shouldReceive('getAnswer')
            ->andReturn(new Answer([
                'id' => Uuid::uuid4(),
                'application_stage_version_id' => $applicationStageVersion->id,
                'field_id' => $field->id,
                'value' => 'some value',
            ]));

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileRepository($mockApplicationFileRepository);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStageVersion($applicationStageVersion);

        $rule->validate('uploadField', null, fn ($message) => $this->assertEquals('File not found!', $message));
    }

    public function testUploadFieldRequiredAndFileExists(): void
    {
        $this->expectNotToPerformAssertions();

        $applicationStageVersionUuid = Uuid::uuid4();

        $applicationStageVersion = Mockery::mock(ApplicationStageVersion::class);

        $applicationStageVersion
            ->expects('getAttribute')
            ->with('id')
            ->andReturn($applicationStageVersionUuid);
        $applicationStageVersion
            ->expects('getAttribute')
            ->with('applicationStage')
            ->andReturn(new ApplicationStage([
                'id' => $applicationStageVersionUuid,
                'application_form_id' => Uuid::uuid4()
            ]));

        $field = new Field([
            'id' => Uuid::uuid4(),
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => true,
        ]);

        $mockApplicationFileRepository = Mockery::mock(ApplicationFileRepository::class);
        $mockApplicationFileRepository->shouldReceive('fileExists')
            ->andReturn(true);

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
        $mockApplicationRepository->shouldReceive('getAnswer')
            ->andReturn(new Answer([
                'id' => Uuid::uuid4(),
                'application_stage_version_id' => $applicationStageVersion->id,
                'field_id' => $field->id,
                'value' => 'some value',
            ]));

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileRepository($mockApplicationFileRepository);
        $rule->setApplicationRepository($mockApplicationRepository);
        $rule->setApplicationStageVersion($applicationStageVersion);

        $rule->validate('uploadField', null, fn ($message) => $this->fail('Should not fail!'));
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
