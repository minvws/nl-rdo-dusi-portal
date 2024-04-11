<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;

class SubsidyStageTransitionsSeeder extends Seeder
{
    public const TRANSITION_STAGE_1_TO_2 = 'd2cfb1d8-ae62-4463-b11b-7db6278901e8';
    public const TRANSITION_STAGE_1_TO_2_TIMEOUT = '25892c15-3262-4550-af2b-3f59c260b656';
    public const TRANSITION_STAGE_2_TO_1 = '0bec9a19-e79c-4efe-831e-5ab7b6910e76';
    public const TRANSITION_STAGE_2_TO_3 = 'ea9925e9-5cbb-4f1b-8292-f5c7cdf7478d';
    public const TRANSITION_STAGE_3_TO_2 = '1263b1b6-8e50-46ff-a0d3-2ce80d9ef939';
    public const TRANSITION_STAGE_3_TO_4 = 'e4140bff-9183-4790-a7b4-da7566786681';
    public const TRANSITION_STAGE_4_TO_2 = '88fda294-5428-4a7a-9f71-ab67db11c5c7';
    public const TRANSITION_STAGE_4_TO_5 = 'd82b9133-a3f6-4088-ad4a-17be84b49613';
    public const TRANSITION_STAGE_5_TO_2 = '73fb1be3-b3a8-4e57-b3fa-97ade2be26a6';
    public const TRANSITION_STAGE_5_TO_APPROVED = 'ed12757d-b2cf-4164-bc62-9ac54b665921';
    public const TRANSITION_STAGE_5_TO_REJECTED = '7a49bdad-854b-468c-a17f-0cb0d091b186';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $encoder = new JSONEncoder();

        // Inzenden aanvraag, aanvraag wordt beschikbaar gesteld voor de eerste beoordeling
        // (of herbeoordeling na aanvulling)
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_1_TO_2,
                'description' => 'Aanvraag ingediend',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_1_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
                'target_application_status' => ApplicationStatus::Pending,
                'condition' => null,
                'send_message' => false,
                'assign_to_previous_assessor' => true,
                'clone_data' => true,
            ]
        );

        // User did not respond in time.
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_1_TO_2_TIMEOUT,
                'description' => 'Geen aanvulling ingediend binnen gestelde termijn',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_1_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
                'target_application_status' => ApplicationStatus::Pending,
                'condition' => null,
                'send_message' => false,
                'assign_to_previous_assessor' => true,
                'evaluation_trigger' => EvaluationTrigger::Expiration->value,
                'clone_data' => true, // clones data of previous assessment
            ]
        );

        // Eerste beoordeling = Aanvulling nodig; aanvraag wordt teruggezet naar de aanvrager om te laten aanvullen
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_2_TO_1,
                'description' => 'Aanvulling gevraagd',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_1_UUID,
                'target_application_status' => ApplicationStatus::RequestForChanges->value,
                'condition' => $encoder->encode(
                    new ComparisonCondition(
                        stage: 2,
                        fieldCode: 'firstAssessment',
                        operator: Operator::Identical,
                        value: 'Aanvulling nodig'
                    )
                ),
                'send_message' => true,
                'clone_data' => true,
                'expiration_period' => 14,
            ]
        );

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_2_TO_3,
                'description' => 'Eerste beoordeling voltooid',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_3_UUID,
                'condition' => $encoder->encode(
                    new InCondition(
                        stage: 2,
                        fieldCode: 'firstAssessment',
                        values: ['Goedgekeurd', 'Afgekeurd']
                    )
                ),
                'send_message' => false,
            ]
        );

        // Bij een beoordeeloptie 'Oneens met de eerste beoordeling' moet de aanvraag opnieuw
        // volledig beoordeeld worden
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_3_TO_2,
                'description' => 'Tweede beoordeling oneens met eerste beoordeling',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_3_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
                'condition' => $encoder->encode(
                    new ComparisonCondition(
                        stage: 3,
                        fieldCode: 'secondAssessment',
                        operator: Operator::Identical,
                        value: 'Oneens met de eerste beoordeling'
                    )
                ),
                'send_message' => false,
                'assign_to_previous_assessor' => true,
                'clone_data' => true,
            ]
        );

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling', wordt de aanvraag als volgt doorgezet:
        // - Eerste beoordeling = goedgekeurd of afgekeurd; aanvraag gaat naar de derde beoordelaar
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_3_TO_4,
                'description' => 'Tweede beoordeling eens met eerste beoordeling',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_3_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_4_UUID,
                'condition' => $encoder->encode(
                    new AndCondition(
                        [
                            new InCondition(
                                stage: 2,
                                fieldCode: 'firstAssessment',
                                values: ['Afgekeurd', 'Goedgekeurd']
                            ),
                            new ComparisonCondition(
                                stage: 3,
                                fieldCode: 'secondAssessment',
                                operator: Operator::Identical,
                                value: 'Eens met de eerste beoordeling'
                            ),
                        ]
                    )
                ),
                'send_message' => false,
            ]
        );

        // Bij een beoordeeloptie 'Goedgekeurd' of 'Afgekeurd' door de eerste beoordelaar en
        // 'Oneens met de eerste beoordeling' door de IC, moet de aanvraag volledig opnieuw beoordeeld worden.
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_4_TO_2,
                'description' => 'Interne controle oneens met beoordeling',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_4_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
                'condition' => $encoder->encode(
                    new AndCondition(
                        [
                            new InCondition(
                                stage: 2,
                                fieldCode: 'firstAssessment',
                                values: ['Afgekeurd', 'Goedgekeurd']
                            ),
                            new ComparisonCondition(
                                stage: 4,
                                fieldCode: 'internalAssessment',
                                operator: Operator::Identical,
                                value: 'Oneens met de eerste beoordeling'
                            ),
                        ]
                    ),
                ),
                'send_message' => false,
                'assign_to_previous_assessor' => true,
                'clone_data' => true,
            ]
        );

        // Bij een beoordeeloptie 'Goedgekeurd' of 'Afgekeurd' door de eerste beoordelaar en 'Goedgekeurd' door de IC,
        // wordt de aanvraag doorgezet naar de vierde beoordeling
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_4_TO_5,
                'description' => 'Interne controle eens met beoordeling',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_4_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_5_UUID,
                'condition' => $encoder->encode(
                    new AndCondition(
                        [
                            new InCondition(
                                stage: 2,
                                fieldCode: 'firstAssessment',
                                values: ['Afgekeurd', 'Goedgekeurd']
                            ),
                            new ComparisonCondition(
                                stage: 4,
                                fieldCode: 'internalAssessment',
                                operator: Operator::Identical,
                                value: 'Eens met de eerste beoordeling'
                            ),
                        ]
                    )
                ),
                'send_message' => false,
            ]
        );

        // Bij een beoordeeloptie 'Goedgekeurd' of 'Afgekeurd' door de eerste beoordelaar en
        // 'Oneens met de eerste beoordeling' door de UC, moet de aanvraag volledig opnieuw beoordeeld worden.
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_5_TO_2,
                'description' => 'Uitvoeringscoördinator oneens met eerste beoordeling',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_5_UUID,
                'target_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_2_UUID,
                'condition' => $encoder->encode(
                    new AndCondition(
                        [
                            new InCondition(
                                stage: 2,
                                fieldCode: 'firstAssessment',
                                values: ['Afgekeurd', 'Goedgekeurd']
                            ),
                            new ComparisonCondition(
                                stage: 5,
                                fieldCode: 'implementationCoordinatorAssessment',
                                operator: Operator::Identical,
                                value: 'Oneens met de eerste beoordeling'
                            ),
                        ]
                    )
                ),
                'send_message' => false,
                'assign_to_previous_assessor' => true,
                'clone_data' => true,
            ]
        );

        // Bij een beoordeeloptie 'Afgekeurd' door de eerste beoordelaar en 'Eens met de eerste beoordeling' door
        // de UC, wordt de aanvraag afgekeurd, en moet de UC een motivatie invoeren en wordt vervolgens een
        // afwijzingsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_5_TO_REJECTED,
                'description' => 'Uitvoeringscoördinator eens met afkeuring in eerste beoordeling',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_5_UUID,
                'target_subsidy_stage_id' => null,
                'target_application_status' => ApplicationStatus::Rejected->value,
                'condition' => $encoder->encode(
                    new AndCondition(
                        [
                            new ComparisonCondition(
                                stage: 2,
                                fieldCode: 'firstAssessment',
                                operator: Operator::Identical,
                                value: 'Afgekeurd'
                            ),
                            new ComparisonCondition(
                                stage: 5,
                                fieldCode: 'implementationCoordinatorAssessment',
                                operator: Operator::Identical,
                                value: 'Eens met de eerste beoordeling'
                            ),
                        ]
                    )
                ),
                'send_message' => true,
            ]
        );

        // Bij een beoordeeloptie 'Goedgekeurd' door de eerste beoordelaar en 'Eens met de eerste beoordeling' door
        // de UC, wordt de aanvraag definitief goedgekeurd, en wordt een toekenningsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert(
            [
                'id' => self::TRANSITION_STAGE_5_TO_APPROVED,
                'description' => 'Uitvoeringscoördinator eens met goedkeuring in eerste beoordeling',
                'current_subsidy_stage_id' => SubsidyStagesSeeder::STAGE_5_UUID,
                'target_subsidy_stage_id' => null,
                'target_application_status' => ApplicationStatus::Approved->value,
                'condition' => $encoder->encode(
                    new AndCondition(
                        [
                            new ComparisonCondition(
                                stage: 2,
                                fieldCode: 'firstAssessment',
                                operator: Operator::Identical,
                                value: 'Goedgekeurd'
                            ),
                            new ComparisonCondition(
                                stage: 5,
                                fieldCode: 'implementationCoordinatorAssessment',
                                operator: Operator::Identical,
                                value: 'Eens met de eerste beoordeling'
                            ),
                        ]
                    )
                ),
                'send_message' => true,
            ]
        );
    }
}
