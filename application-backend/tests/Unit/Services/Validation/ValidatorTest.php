<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
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
}
