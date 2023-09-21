<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Models\DTO;

use Generator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use PHPUnit\Framework\TestCase;

/**
 * @group condition
 */
class ConditionTest extends TestCase
{
    public function testEncoding(): void
    {
        $inputCondition =
            new AndCondition([
                new ComparisonCondition(2, 'decision', Operator::Equal, 'reject'),
                new OrCondition([
                    new ComparisonCondition(1, 'number', Operator::GreaterThan, 5),
                    new ComparisonCondition(1, 'otherValue', Operator::NotEqual, 'value')
                ])
            ]);

        $expectedData =  [
            "type" => "and",
            "conditions" => [
                [
                    "type" => "comparison",
                    "stage" => 2,
                    "fieldCode" => "decision",
                    "operator" => "==",
                    "value" => "reject"
                ],
                [
                    "type" => "or",
                    "conditions" => [
                        [
                            "type" => "comparison",
                            "stage" => 1,
                            "fieldCode" => "number",
                            "operator" => ">",
                            "value" => 5
                        ],
                        [
                            "type" => "comparison",
                            "stage" => 1,
                            "fieldCode" => "otherValue",
                            "operator" => "!=",
                            "value" => "value"
                        ]
                    ]
                ]
            ]
        ];

        $cast = Condition::castUsing([]);
        $attrs = $cast->set(new SubsidyStageTransition(), 'condition', $inputCondition, []);
        $this->assertEquals($expectedData, json_decode($attrs['condition'], true));
    }

    public function testDecoding(): void
    {
        $inputData =  json_encode([
            "type" => "and",
            "conditions" => [
                [
                    "type" => "comparison",
                    "stage" => 2,
                    "fieldCode" => "decision",
                    "operator" => "==",
                    "value" => "reject"
                ],
                [
                    "type" => "or",
                    "conditions" => [
                        [
                            "type" => "comparison",
                            "stage" => 1,
                            "fieldCode" => "number",
                            "operator" => ">",
                            "value" => 5
                        ],
                        [
                            "type" => "comparison",
                            "stage" => 1,
                            "fieldCode" => "otherValue",
                            "operator" => "!=",
                            "value" => "value"
                        ]
                    ]
                ]
            ]
        ]);

        $expectedCondition =
            new AndCondition([
                new ComparisonCondition(2, 'decision', Operator::Equal, 'reject'),
                new OrCondition([
                    new ComparisonCondition(1, 'number', Operator::GreaterThan, 5),
                    new ComparisonCondition(1, 'otherValue', Operator::NotEqual, 'value')
                ])
            ]);

        $cast = Condition::castUsing([]);
        $condition = $cast->get(new SubsidyStageTransition(), 'condition', $inputData, []);
        $this->assertEquals($expectedCondition, $condition);
    }

    public static function conditionProvider(): Generator
    {
        yield [
            new ComparisonCondition(1, 'key1', Operator::Identical, 'value1'),
            true
        ];
        yield [
            new ComparisonCondition(1, 'key3', Operator::Equal, 3),
            true
        ];
        yield [
            new ComparisonCondition(1, 'key3', Operator::Equal, '3'),
            true
        ];
        yield [
            new ComparisonCondition(1, 'key3', Operator::Identical, 3),
            true
        ];
        yield [
            new ComparisonCondition(1, 'key3', Operator::Identical, '3'),
            false
        ];
        yield [
            new AndCondition([
                new ComparisonCondition(1, 'key1', Operator::Identical, 'value1'),
                new ComparisonCondition(2, 'key1', Operator::Identical, 1)
            ]),
            true
        ];
        yield [
            new OrCondition([
                new ComparisonCondition(1, 'key1', Operator::Identical, 'value2'),
                new ComparisonCondition(2, 'key1', Operator::Identical, 1)
            ]),
            true
        ];
        yield [
            new InCondition(1, 'key2', ['value2', 'value3']),
            true
        ];
        yield [
            new InCondition(1, 'key2', ['value1', 'value3']),
            false
        ];
    }

    /**
     * @dataProvider conditionProvider
     */
    public function testEvaluate(Condition $condition, bool $expected): void
    {
        $data = [
            1 => (object)[
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 3
            ],
            2 => (object)[
                'key1' => 1,
                'key4' => 'value4'
            ]
        ];

        $this->assertEquals($expected, $condition->evaluate($data));
    }
}
