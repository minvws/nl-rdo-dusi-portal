<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Services\Validation\Rules;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\File;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\FileUploadRule;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class FileUploadRuleTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected ApplicationFileManager $applicationFileManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationFileManager = Mockery::mock(ApplicationFileManager::class);
    }

    public function testUploadFieldNotRequiredAndValueNull(): void
    {
        $this->expectNotToPerformAssertions();

        $field = new Field([
            'type' => FieldType::Upload,
            'code' => 'uploadField',
            'title' => 'Upload field',
            'is_required' => false,
        ]);

        $rule = new FileUploadRule($field);

        $rule->validate('uploadField', null, fn ($message) => $this->fail('Should not fail!'));
    }

    public function testUploadFieldNotRequiredAndValueNotNullAndFileExists(): void
    {
        $this->expectNotToPerformAssertions();

        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = $this->getUploadField(true);

        $fileUuid = Uuid::uuid4()->toString();

        $this->applicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(true);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($this->applicationFileManager);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn ($message) => $this->fail('Should not fail!'));
    }
    public function testRequiredAndNullValue(): void
    {
        $field = $this->getUploadField(true);

        $rule = new FileUploadRule($field);
        $rule->validate('testUploadField', null, function ($message) {
            $this->assertSame('Field is required!', $message);
        });
    }

    public function testValueIsNotFileList(): void
    {
        $field = $this->getUploadField(true);

        $rule = new FileUploadRule($field);
        $rule->validate('testUploadField', 'not a file list', function ($message) {
            $this->assertSame('Field is required!', $message);
        });
    }

    public function testUploadFieldNotRequiredAndValueNotNullAndFileNotExists(): void
    {
        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = $this->getUploadField(true);

        $fileUuid = Uuid::uuid4()->toString();

        $this->applicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(false);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($this->applicationFileManager);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn ($message) => $this->assertEquals('File not found!', $message));
    }

    public function testUploadFieldRequiredAndFileNotExists(): void
    {
        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = $this->getUploadField(true);

        $fileUuid = Uuid::uuid4()->toString();

        $this->applicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(false);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($this->applicationFileManager);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn ($message) => $this->assertEquals('File not found!', $message));
    }

    public function testUploadFieldRequiredAndFileExists(): void
    {
        $this->expectNotToPerformAssertions();

        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = $this->getUploadField(true);

        $fileUuid = Uuid::uuid4()->toString();

        $this->applicationFileManager->shouldReceive('fileExists')
            ->withArgs([$applicationStage, $field, $fileUuid])
            ->andReturn(true);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($this->applicationFileManager);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList([
            new File($fileUuid, 'file1.pdf', 'application/pdf')
        ]), fn($message) => $this->fail('Should not fail!'));
    }

    /**
     * @dataProvider minMaxItemsProvider
     * @param bool $performAssertions
     * @param int $itemsCount
     * @param int|null $minItems
     * @param int|null $maxItems
     * @param string|null $failMessage
     * @return void
     */
    public function testMinAndMaxItems(
        bool $performAssertions,
        bool $required,
        int $itemsCount,
        ?int $minItems = null,
        ?int $maxItems = null,
        ?string $failMessage = null
    ): void {
        if (!$performAssertions) {
            $this->expectNotToPerformAssertions();
        }

        $applicationStage = Mockery::mock(ApplicationStage::class);

        $field = $this->getUploadField($required, $minItems, $maxItems);

        $this->applicationFileManager
            ->shouldReceive('fileExists')
            ->andReturn(true);

        $rule = new FileUploadRule($field);
        $rule->setApplicationFileManager($this->applicationFileManager);
        $rule->setApplicationStage($applicationStage);

        $rule->validate('uploadField', new FileList(
            Collection::times(
                $itemsCount,
                fn() => new File(Uuid::uuid4()->toString(), 'file1.pdf', 'application/pdf')
            )->toArray()
        ), function ($message) use ($failMessage) {
            if ($failMessage !== null) {
                $this->assertEquals($failMessage, $message);
            } else {
                $this->fail('Should not fail!');
            }
        });
    }

    public static function minMaxItemsProvider(): array
    {
        return [
            'no min and max without items' => [false, true, 0, null, null, null],
            'no min and max with 1 item' => [false, true, 1, null, null, null],
            'minimum of 1 with 1 item' => [false, true, 1, 1, null, null],
            'minimum of 1 with 2 items' => [false, true, 2, 1, null, null],
            'minimum of 2 with 1 item' => [true, true, 1, 2, null, 'Minimum number of files not met!'],
            'minimum of 2 with 2 items' => [false, true, 2, 2, null, null],
            'not required maximum of 1 with 2 items' => [true, false, 2, null, 1, 'Maximum number of files exceeded!'],
            'maximum of 1 with 1 item' => [false, true, 1, null, 1, null],
            'maximum of 1 with 2 items' => [true, true, 2, null, 1, 'Maximum number of files exceeded!'],
            'maximum of 2 with 1 item' => [false, true, 1, null, 2, null],
            'maximum of 2 with 2 items' => [false, true, 2, null, 2, null],
            'minimum of 1 and maximum of 2 with 1 item' => [false, true, 1, 1, 2, null],
            'minimum of 1 and maximum of 2 with 2 items' => [false, true, 2, 1, 2, null],
            'minimum of 1 and maximum of 2 with 3 items' => [true, true, 3, 1, 2, 'Maximum number of files exceeded!'],
            'not required minimum of 1 with 0 items' => [false, false, 0, 1, null, null],
            'not required minimum of 2 with 1 items' => [true, false, 1, 2, null, 'Minimum number of files not met!'],
            'not required minimum of 2 with 2 items' => [false, false, 2, 2, null, null],
            'not required maximum of 1 with 0 items' => [false, false, 0, null, 1, null],
            'not required maximum of 1 with 1 items' => [false, false, 1, null, 1, null],
            'not required maximum of 2 with 3 items' => [true, false, 3, null, 2, 'Maximum number of files exceeded!'],
        ];
    }

    protected function getUploadField(bool $isRequired, ?int $minItems = null, ?int $maxItems = null): Field
    {
        $field = new Field();
        $field->id = Uuid::uuid4();
        $field->type = FieldType::Upload;
        $field->code = 'uploadField';
        $field->title = 'Upload field';
        $field->is_required = $isRequired;
        $field->params = array_filter([
            'minItems' => $minItems,
            'maxItems' => $maxItems,
        ]);

        return $field;
    }
}
