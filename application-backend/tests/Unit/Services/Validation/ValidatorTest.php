<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services\Validation;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationFileRepositoryAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationRepositoryAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\ApplicationStageVersionAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\FieldValuesAwareRule;
use MinVWS\DUSi\Application\Backend\Services\Validation\Validator;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use Mockery;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
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

        $applicationStageVersion = new ApplicationStageVersion();

        $mockApplicationFileRepository = Mockery::mock(ApplicationFileRepository::class);
        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStageVersion: $applicationStageVersion,
            fieldValues: $fieldValues,
            applicationFileRepository: $mockApplicationFileRepository,
            applicationRepository: $mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }

    /**
     * Only test if we can have a validation rule, and it is an ApplicationStageVersionAwareRule.
     * And that the application stage version is set on the rule.
     */
    public function testApplicationStageVersionAwareRule(): void
    {
        $applicationStageVersion = Mockery::mock(ApplicationStageVersion::class);

        $mockRule = Mockery::mock(ValidationRule::class, ApplicationStageVersionAwareRule::class);
        $mockRule
            ->shouldReceive('setApplicationStageVersion')
            ->with($applicationStageVersion)
            ->once();

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $mockApplicationFileRepository = Mockery::mock(ApplicationFileRepository::class);
        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStageVersion: $applicationStageVersion,
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileRepository: $mockApplicationFileRepository,
            applicationRepository: $mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }

    /**
     * Only test if we can have a validation rule, and it is an ApplicationRepositoryAwareRule.
     * And that the application repository is set on the rule.
     */
    public function testApplicationRepositoryAwareRule(): void
    {
        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $mockRule = Mockery::mock(ValidationRule::class, ApplicationRepositoryAwareRule::class);
        $mockRule
            ->shouldReceive('setApplicationRepository')
            ->with($mockApplicationRepository)
            ->once();

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $mockApplicationFileRepository = Mockery::mock(ApplicationFileRepository::class);

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStageVersion: new ApplicationStageVersion(),
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileRepository: $mockApplicationFileRepository,
            applicationRepository: $mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }

    /**
     * Only test if we can have a validation rule, and it is an ApplicationFileRepositoryAwareRule.
     * And that the application file repository is set on the rule.
     */
    public function testApplicationFileRepositoryAwareRule(): void
    {
        $applicationFileRepository = Mockery::mock(ApplicationFileRepository::class);

        $mockRule = Mockery::mock(ValidationRule::class, ApplicationFileRepositoryAwareRule::class);
        $mockRule
            ->shouldReceive('setApplicationFileRepository')
            ->with($applicationFileRepository)
            ->once();

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStageVersion: new ApplicationStageVersion(),
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileRepository: $applicationFileRepository,
            applicationRepository: $mockApplicationRepository,
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

        $mockRule
            ->shouldReceive('validate')
            ->with('someField', 'some value', Mockery::type(Closure::class))
            ->once();

        $mockApplicationRepository = Mockery::mock(ApplicationRepository::class);

        $validator = new Validator(
            translator: new Translator(new ArrayLoader(), 'nl'),
            data: [
                'someField' => 'some value',
            ],
            rules: [
                'someField' => $mockRule,
            ],
            applicationStageVersion: new ApplicationStageVersion(),
            fieldValues: [
                'someField' => new FieldValue(new Field(), 'some value'),
                'anotherField' => new FieldValue(new Field(), 'another value'),
            ],
            applicationFileRepository: Mockery::mock(ApplicationFileRepository::class),
            applicationRepository: $mockApplicationRepository,
        );

        self::assertTrue($validator->passes());
    }
}
