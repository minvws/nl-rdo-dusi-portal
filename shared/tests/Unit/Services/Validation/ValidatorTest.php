<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Services\Validation;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationFileManagerAwareRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationRepositoryAwareRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\ApplicationStageAwareRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\FieldValuesAwareRule;
use MinVWS\DUSi\Shared\Application\Services\Validation\Validator;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ApplicationFileManager $mockApplicationFileManager;
    private ApplicationRepository $mockApplicationRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockApplicationFileManager = Mockery::mock(ApplicationFileManager::class);
        $this->mockApplicationRepository = Mockery::mock(ApplicationRepository::class);
    }

    /**
     * Only test if we can have a validation rule, and it is an FieldValuesAwareRule.
     * And that the field values are set on the rule.
     */
    public function testFieldValuesAwareRule(): void
    {
        $fieldValues = [
            'someField' => new FieldValue(new Field(), 'some value'),
            'anotherField' => new FieldValue(new Field(), 'another value'),
        ];

        $mockRule = Mockery::mock(ValidationRule::class, FieldValuesAwareRule::class);
        $mockRule
            ->shouldReceive('setFieldValues')
            ->with($fieldValues)
            ->once();

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $applicationStage = new ApplicationStage();

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => [$mockRule],
            ],
            applicationStage: $applicationStage,
            fieldValues: $fieldValues,
            applicationFileManager: $this->mockApplicationFileManager,
            applicationRepository: $this->mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }

    /**
     * Only test if we can have a validation rule, and it is an ApplicationStageVersionAwareRule.
     * And that the application stage version is set on the rule.
     */
    public function testApplicationStageVersionAwareRule(): void
    {
        $applicationStage = Mockery::mock(ApplicationStage::class);

        $mockRule = Mockery::mock(ValidationRule::class, ApplicationStageAwareRule::class);
        $mockRule
            ->shouldReceive('setApplicationStage')
            ->with($applicationStage)
            ->once();

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStage: $applicationStage,
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileManager: $this->mockApplicationFileManager,
            applicationRepository: $this->mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }

    /**
     * Only test if we can have a validation rule, and it is an ApplicationRepositoryAwareRule.
     * And that the application repository is set on the rule.
     */
    public function testApplicationRepositoryAwareRule(): void
    {
        $mockRule = Mockery::mock(ValidationRule::class, ApplicationRepositoryAwareRule::class);
        $mockRule
            ->shouldReceive('setApplicationRepository')
            ->with($this->mockApplicationRepository)
            ->once();

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStage: new ApplicationStage(),
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileManager: $this->mockApplicationFileManager,
            applicationRepository: $this->mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }

    /**
     * Only test if we can have a validation rule, and it is an ApplicationFileRepositoryAwareRule.
     * And that the application file repository is set on the rule.
     */
    public function testApplicationFileRepositoryAwareRule(): void
    {
        $mockRule = Mockery::mock(ValidationRule::class, ApplicationFileManagerAwareRule::class);
        $mockRule
            ->shouldReceive('setApplicationFileManager')
            ->with($this->mockApplicationFileManager)
            ->once();

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStage: new ApplicationStage(),
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileManager: $this->mockApplicationFileManager,
            applicationRepository: $this->mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }

    /**
     * Test validator works with normal 'Rule'.
     * Not to test the rule specific but to check if our validator calls parent.
     */
    public function testValidatorWithRule(): void
    {
        $mockRule = Mockery::mock(Rule::class);
        $mockRule->shouldReceive('passes')
            ->andReturn(true);

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => [$mockRule],
            ],
            applicationStage: new ApplicationStage(),
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileManager: $this->mockApplicationFileManager,
            applicationRepository: $this->mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }
}
