<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;

class SubsidyStageTransitionsTableSeeder extends Seeder
{
    public const PZCM_TRANSITION_STAGE_1_TO_2 = '7ac879d1-63cb-478d-8745-737313f1643e';
    public const PZCM_TRANSITION_STAGE_2_TO_3 = 'dd630ec0-50d1-45f5-b014-415e6359389e';
    public const PZCM_TRANSITION_STAGE_3_TO_2 = 'c33b8459-3a98-4906-9ce0-c6f9c0ae7a49';
    public const PZCM_TRANSITION_STAGE_3_TO_1 = '870bc38a-0d50-40a9-b49e-d56db5ead6b7';
    public const PZCM_TRANSITION_STAGE_3_TO_4 = 'd73eacca-7605-4915-9efa-bba7c92c3a46';
    public const PZCM_TRANSITION_STAGE_4_TO_REJECTED = 'c2080b04-1389-42d1-9aca-33141f01a3bc';
    public const PZCM_TRANSITION_STAGE_4_TO_2 = '005a5acb-a908-44d2-8b69-a50d5ef43870';
    public const PZCM_TRANSITION_STAGE_4_TO_5 = '3286f4cf-87ae-4cfc-9c1d-523b2ec6745a';
    public const PZCM_TRANSITION_STAGE_5_TO_APPROVED = 'a27195df-9825-4d18-acce-9b3492221d8a';
    public const PZCM_TRANSITION_STAGE_5_TO_REJECTED = '963a5afa-6990-4ea9-b097-91999c863d6c';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $encoder = new JSONEncoder();

        // Inzenden aanvraag, aanvraag wordt beschikbaar gesteld voor de eerste beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_1_TO_2,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            'target_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            'target_application_status' => ApplicationStatus::Submitted,
            'condition' => null,
            'send_message' => false
        ]);

        // Eerste beoordeling voltooid, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_2_TO_3,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            'target_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            'condition' => null,
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Oneens met de eerste beoordeling' moet de aanvraag opnieuw
        // volledig beoordeeld worden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_3_TO_2,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            'target_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    3,
                    'secondAssessment',
                    Operator::Identical,
                    'Oneens met de eerste beoordeling'
                )
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling', wordt de aanvraag als volgt doorgezet:
        // - Eerste beoordeling = Aanvulling nodig; aanvraag wordt teruggezet naar de aanvrager om te laten aanvullen
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_3_TO_1,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            'target_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            'target_application_status' => ApplicationStatus::RequestForChanges->value,
            'condition' => $encoder->encode(
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        'Aanvulling nodig'
                    ),
                    new ComparisonCondition(
                        3,
                        'secondAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => true
        ]);

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling', wordt de aanvraag als volgt doorgezet:
        // - Eerste beoordeling = goedgekeurd; aanvraag gaat naar de derde beoordelaar
        // - Eerste beoordeling = afgekeurd; aanvraag gaat naar de derde beoordelaar
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_3_TO_4,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            'target_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            'condition' => $encoder->encode(
                new AndCondition([
                    new InCondition(
                        2,
                        'firstAssessment',
                        ['Goedgekeurd', 'Afgekeurd']
                    ),
                    new ComparisonCondition(
                        3,
                        'secondAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Afgekeurd' door de eerste beoordelaar en 'Afgekeurd' door de IC, wordt de
        // aanvraag afgekeurd en wordt een afwijzingsbrief verzonden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_4_TO_REJECTED,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Rejected->value,
            'condition' => $encoder->encode(
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        'Afgekeurd'
                    ),
                    new ComparisonCondition(
                        3,
                        'internalAssessment',
                        Operator::Identical,
                        'Afgekeurd'
                    )
                ])
            ),
            'send_message' => true
        ]);

        // Bij een beoordeeloptie 'Afgekeurd' door de eerste beoordelaar en 'Goedgekeurd' door de IC,
        // moet de aanvraag volledig opnieuw beoordeeld worden.
        // Bij een beoordeeloptie 'Goedgekeurd' door de eerste beoordelaar en 'Afgekeurd' door de IC,
        // moet de aanvraag volledig opnieuw beoordeeld worden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_4_TO_2,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            'target_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            'condition' => $encoder->encode(
                new OrCondition([
                    new AndCondition([
                        new ComparisonCondition(
                            2,
                            'firstAssessment',
                            Operator::Identical,
                            'Afgekeurd'
                        ),
                        new ComparisonCondition(
                            3,
                            'internalAssessment',
                            Operator::Identical,
                            'Goedgekeurd'
                        )
                    ]),
                    new AndCondition([
                        new ComparisonCondition(
                            2,
                            'firstAssessment',
                            Operator::Identical,
                            'Goedgekeurd'
                        ),
                        new ComparisonCondition(
                            3,
                            'internalAssessment',
                            Operator::Identical,
                            'Afgekeurd'
                        )
                    ])
                ])
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Goedgekeurd' door de eerste beoordelaar en 'Goedgekeurd' door de IC,
        // wordt de aanvraag doorgezet naar de vierde beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_4_TO_5,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            'target_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            'condition' => $encoder->encode(
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        'Goedgekeurd'
                    ),
                    new ComparisonCondition(
                        3,
                        'internalAssessment',
                        Operator::Identical,
                        'Goedgekeurd'
                    )
                ])
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Afgekeurd' wordt de aanvraag afgekeurd, en moet de UC een motivatie
        // invoeren en wordt vervolgens een afwijzingsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_5_TO_REJECTED,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Rejected->value,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    5,
                    'implementationCoordinatorAssessment',
                    Operator::Identical,
                    'Afgekeurd'
                )
            ),
            'send_message' => true
        ]);

        // Bij een beoordeeloptie 'Goedgekeurd' wordt de aanvraag definitief goedgekeurd en wordt een
        // toekenningsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_5_TO_APPROVED,
            'current_subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Approved->value,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    5,
                    'implementationCoordinatorAssessment',
                    Operator::Identical,
                    'Goedgekeurd'
                )
            ),
            'send_message' => true
        ]);
    }
}
