<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Models\DTO;

use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
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
                new ComparisonCondition('decision', Operator::Equal, 'reject'),
                new OrCondition([
                    new ComparisonCondition('number', Operator::GreaterThan, 5),
                    new ComparisonCondition('otherValue', Operator::NotEqual, 'value')
                ])
            ]);

        $expectedData =  [
            "type" => "and",
            "conditions" => [
                [
                    "type" => "comparison",
                    "fieldCode" => "decision",
                    "operator" => "==",
                    "value" => "reject"
                ],
                [
                    "type" => "or",
                    "conditions" => [
                        [
                            "type" => "comparison",
                            "fieldCode" => "number",
                            "operator" => ">",
                            "value" => 5
                        ],
                        [
                            "type" => "comparison",
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
                    "fieldCode" => "decision",
                    "operator" => "==",
                    "value" => "reject"
                ],
                [
                    "type" => "or",
                    "conditions" => [
                        [
                            "type" => "comparison",
                            "fieldCode" => "number",
                            "operator" => ">",
                            "value" => 5
                        ],
                        [
                            "type" => "comparison",
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
                new ComparisonCondition('decision', Operator::Equal, 'reject'),
                new OrCondition([
                    new ComparisonCondition('number', Operator::GreaterThan, 5),
                    new ComparisonCondition('otherValue', Operator::NotEqual, 'value')
                ])
            ]);

        $cast = Condition::castUsing([]);
        $condition = $cast->get(new SubsidyStageTransition(), 'condition', $inputData, []);
        $this->assertEquals($expectedCondition, $condition);
    }
}
