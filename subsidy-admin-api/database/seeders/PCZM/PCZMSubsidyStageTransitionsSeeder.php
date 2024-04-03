<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

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

class PCZMSubsidyStageTransitionsSeeder extends Seeder
{
    public const PZCM_TRANSITION_STAGE_1_TO_2 = '7ac879d1-63cb-478d-8745-737313f1643e';
    public const PZCM_TRANSITION_STAGE_1_TO_2_TIMEOUT = '5b8b3dcb-717d-4ff0-9698-800120285298';
    public const PZCM_TRANSITION_STAGE_2_TO_1 = '870bc38a-0d50-40a9-b49e-d56db5ead6b7';
    public const PZCM_TRANSITION_STAGE_2_TO_3 = 'dd630ec0-50d1-45f5-b014-415e6359389e';
    public const PZCM_TRANSITION_STAGE_3_TO_REJECTED = 'c2080b04-1389-42d1-9aca-33141f01a3bc';
    public const PZCM_TRANSITION_STAGE_3_TO_2 = 'c33b8459-3a98-4906-9ce0-c6f9c0ae7a49';
    public const PZCM_TRANSITION_STAGE_3_TO_4 = 'd73eacca-7605-4915-9efa-bba7c92c3a46';
    public const PZCM_TRANSITION_STAGE_4_TO_2 = '005a5acb-a908-44d2-8b69-a50d5ef43870';
    public const PZCM_TRANSITION_STAGE_4_TO_5 = '3286f4cf-87ae-4cfc-9c1d-523b2ec6745a';
    public const PZCM_TRANSITION_STAGE_5_TO_APPROVED = 'a27195df-9825-4d18-acce-9b3492221d8a';
    public const PZCM_TRANSITION_STAGE_5_TO_REJECTED = '963a5afa-6990-4ea9-b097-91999c863d6c';
    public const PZCM_TRANSITION_STAGE_6_TO_INCREASE_EMAIL = '2b493130-c191-4455-8de4-d932ab6c2b60';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $encoder = new JSONEncoder();

        // Inzenden aanvraag, aanvraag wordt beschikbaar gesteld voor de eerste beoordeling
        // (of herbeoordeling na aanvulling)
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_1_TO_2,
            'description' => 'Aanvraag ingediend',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_1_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            'target_application_status' => ApplicationStatus::Pending,
            'condition' => null,
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);

        // User did not respond in time.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_1_TO_2_TIMEOUT,
            'description' => 'Geen aanvulling ingediend binnen gestelde termijn',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_1_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            'target_application_status' => ApplicationStatus::Pending,
            'condition' => null,
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'evaluation_trigger' => EvaluationTrigger::Expiration->value,
            'clone_data' => true // clones data of previous assessment
        ]);

        // Eerste beoordeling = Aanvulling nodig; aanvraag wordt teruggezet naar de aanvrager om te laten aanvullen
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_2_TO_1,
            'description' => 'Aanvulling gevraagd',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_1_UUID,
            'target_application_status' => ApplicationStatus::RequestForChanges->value,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    2,
                    'firstAssessment',
                    Operator::Identical,
                    'Aanvulling nodig'
                )
            ),
            'send_message' => true,
            'clone_data' => true,
            'expiration_period' => 14
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_2_TO_3,
            'description' => 'Eerste beoordeling voltooid',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
            'condition' => $encoder->encode(
                new InCondition(
                    2,
                    'firstAssessment',
                    ['Goedgekeurd', 'Afgekeurd']
                )
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Oneens met de eerste beoordeling' moet de aanvraag opnieuw
        // volledig beoordeeld worden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_3_TO_2,
            'description' => 'Tweede beoordeling oneens met eerste beoordeling',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    3,
                    'secondAssessment',
                    Operator::Identical,
                    'Oneens met de eerste beoordeling'
                )
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);


        // Bij een beoordeeloptie 'Eens met de eerste beoordeling', wordt de aanvraag als volgt doorgezet:
        // - Eerste beoordeling = afgekeurd; aanvraag wordt afgekeurd en er wordt een afwijzingsbrief verzonden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_3_TO_REJECTED,
            'description' => 'Tweede beoordeling eens met afkeuring eerste beoordeling',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
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
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_3_TO_4,
            'description' => 'Tweede beoordeling eens met goedkeuring eerste beoordeling',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_4_UUID,
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
                        'secondAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Afgekeurd' door de eerste beoordelaar en 'Goedgekeurd' door de IC,
        // moet de aanvraag volledig opnieuw beoordeeld worden.
        // Bij een beoordeeloptie 'Goedgekeurd' door de eerste beoordelaar en 'Afgekeurd' door de IC,
        // moet de aanvraag volledig opnieuw beoordeeld worden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_4_TO_2,
            'description' => 'Interne controle oneens met beoordeling',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_4_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
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
                            4,
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
                            4,
                            'internalAssessment',
                            Operator::Identical,
                            'Afgekeurd'
                        )
                    ])
                ])
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);

        // Bij een beoordeeloptie 'Goedgekeurd' door de eerste beoordelaar en 'Goedgekeurd' door de IC,
        // wordt de aanvraag doorgezet naar de vierde beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_4_TO_5,
            'description' => 'Interne controle eens met beoordeling',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_4_UUID,
            'target_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
            'condition' => $encoder->encode(
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        'Goedgekeurd'
                    ),
                    new ComparisonCondition(
                        4,
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
            'description' => 'Aanvraag afgekeurd',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
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
            'description' => 'Aanvraag goedgekeurd',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
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

        // Een goedgekeurde aanvraag krijgt een brief waarin wordt toegelicht dat het toegewezen bedrag wordt
        // verhoogd.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_6_TO_INCREASE_EMAIL,
            'description' => 'Toegekend bedrag verhoogd',
            'current_subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_6_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => null,
            'condition' => $encoder->encode(
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        'Goedgekeurd'
                    ),
                    new ComparisonCondition(
                        5,
                        'implementationCoordinatorAssessment',
                        Operator::Identical,
                        'Goedgekeurd'
                    )
                ])
            ),
            'send_message' => true
        ]);

    }
}
