<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\Subsidy\Helpers;

use Generator;
use MinVWS\DUSi\Shared\Subsidy\Helpers\RequiredConditionSchemaMapper;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use PHPUnit\Framework\TestCase;

class RequiredConditionSchemaMapperTest extends TestCase
{
    /**
     * @dataProvider providerMapConditions
     * @param Condition $condition
     * @param Field $field
     * @param array $expected
     * @return void
     */
    public function testMapCondition(Condition $condition, Field $field, array $expected): void
    {
        $mapper = new RequiredConditionSchemaMapper();
        $result = $mapper->mapConditionAndFieldToDataScheme($condition, $field);

        $this->assertEquals($expected, $result);
    }

    public static function providerMapConditions(): Generator
    {
        yield [
            new ComparisonCondition(1, 'isWiaDecisionPostponed', Operator::Identical, 'Ja'),
            new Field([
                'code' => 'wiaDecisionPostponedLetter',
            ]),
            [
                "if" => [
                    "required" => ["isWiaDecisionPostponed"],
                    "properties" => [
                        "isWiaDecisionPostponed" => [
                            "const" => "Ja",
                        ],
                    ],
                ],
                "then" => [
                    "required" => [
                        "wiaDecisionPostponedLetter",
                    ],
                ],
            ],
        ];

        yield [
            new OrCondition([
                new ComparisonCondition(1, 'employmentFunction', Operator::Identical, 'Anders'),
                new ComparisonCondition(1, 'employerKind', Operator::Identical, 'Andere organisatie'),
            ]),
            new Field([
                'code' => 'otherEmployerDeclarationFile',
            ]),
            [
                "if" => [
                    "anyOf" => [
                        [
                            "required" => ["employmentFunction"],
                            "properties" => ["employmentFunction" => ["const" => "Anders"]],
                        ],
                        [
                            "required" => ["employerKind"],
                            "properties" => [
                                "employerKind" => ["const" => "Andere organisatie"],
                            ],
                        ],
                    ],
                ],
                "then" => ["required" => ["otherEmployerDeclarationFile"]],
            ],
        ];
    }
}
