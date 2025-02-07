<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Services\Validation\Rules;

use Closure;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\RequiredConditionRule;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RequiredConditionRuleTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public static function validationDataProvider(): array
    {
        return [
            'Value is not empty and field is not required' =>
                ['attribute', 'value', new MockCondition(false), true, null],
            'Value is empty and field is required' =>
                ['attribute', '', new MockCondition(true), false, 'validation.required'],
            'Value is empty and field is not required' =>
                ['attribute', '', new MockCondition(false), true, null],
            'Value is null and field is required' =>
                ['attribute', null, new MockCondition(true), false, 'validation.required'],
            'Value is true and field is not required' =>
                ['attribute', true, new MockCondition(false), true, null],
            'Value is true and field is required' =>
                ['attribute', true, new MockCondition(true), true, null],
            'Value is false and field is required' =>
                ['attribute', false, new MockCondition(true), false, 'validation.required'],
            'Value is empty array and field is not required' =>
                ['attribute', [], new MockCondition(false), true, null],
            'Value is empty array and field is required' =>
                ['attribute', [], new MockCondition(true), false, 'validation.required'],
            'Value is not empty array and field is required' =>
                ['attribute', ['test'], new MockCondition(true), true, null],
            'Value is numeric (0) and field is required' =>
                ['attribute', 0, new MockCondition(true), true, null],
            'Value is numeric (0.0) and field is required' =>
                ['attribute', 0.0, new MockCondition(true), true, null],
            'Value is numeric string (0) and field is required' =>
                ['attribute', '0', new MockCondition(true), true, null],
        ];
    }

    #[DataProvider('validationDataProvider')]
    public function testValidate(
        string $attribute,
        mixed $value,
        MockCondition $condition,
        bool $shouldSucceed,
        ?string $expectedMessage
    ): void {
        $rule = new RequiredConditionRule(1, $condition);
        $rule->setData(['some' => 'data']);

        $fail = $this->testableClosure($expectedMessage, $shouldSucceed);

        $rule->validate($attribute, $value, $fail(...));

        if ($shouldSucceed) {
            $this->assertTrue(true);
        }
    }

    protected function testableClosure(?string $expectedMessage, bool $testShouldSucceed): Closure
    {
        return function ($message) use ($expectedMessage, $testShouldSucceed) {
            $this->assertSame($expectedMessage, $message);

            if ($testShouldSucceed) {
                $this->fail('Fail should not be called.');
            }

            return new class {
                public function translate()
                {
                }
            };
        };
    }
}
