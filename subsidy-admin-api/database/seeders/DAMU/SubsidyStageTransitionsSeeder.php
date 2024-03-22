<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;

class SubsidyStageTransitionsSeeder extends Seeder
{
    public const TRANSITION_STAGE_1_TO_2 = '3c6f4891-3b98-4f15-8e3a-fc81f702d3ae';
    public const TRANSITION_STAGE_1_TO_2_TIMEOUT = '7c8c1355-493a-445e-98e6-aa30d234892e';
    public const TRANSITION_STAGE_2_TO_1 = '1047e69c-9107-47bc-bfe4-78464e6fb8d3';
    public const TRANSITION_STAGE_2_TO_3 = '8c66d02a-9ef7-41ff-a1b6-6c747dcadd0c';
    public const TRANSITION_STAGE_3_TO_2 = 'da58c18a-0645-4404-bbe4-a186babc01e2';
    public const TRANSITION_STAGE_3_TO_4 = '1c375d68-d9bb-4343-b14e-692ce893b64c';
    public const TRANSITION_STAGE_3_TO_REJECTED = '5e938249-b011-4b82-a700-1a4a55170492';
    public const TRANSITION_STAGE_4_TO_2 = '383f4e2b-8d5d-4a64-a3d6-001caa857a31';
    public const TRANSITION_STAGE_4_TO_APPROVED = '7c2c08be-5216-4abb-b8ba-fe08ac922f90';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $encoder = new JSONEncoder();

        // Inzenden aanvraag, aanvraag wordt beschikbaar gesteld voor de eerste beoordeling
        // (of herbeoordeling na aanvulling)
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_1_TO_2,
            'description' => 'Aanvraag ingediend',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'target_application_status' => ApplicationStatus::Pending,
            'condition' => null,
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);

        // User did not respond in time.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_1_TO_2_TIMEOUT,
            'description' => 'Geen aanvulling ingediend binnen gestelde termijn',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'target_application_status' => ApplicationStatus::Pending,
            'condition' => null,
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'evaluation_trigger' => EvaluationTrigger::Expiration,
            'clone_data' => true // clones data of previous assessment
        ]);

        // Eerste beoordeling = Aanvulling nodig; aanvraag wordt teruggezet naar de aanvrager om te laten aanvullen
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_2_TO_1,
            'description' => 'Aanvulling gevraagd',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            'target_application_status' => ApplicationStatus::RequestForChanges,
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
            'expiration_period' => 14,
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet naar de Uitvoeringscoordinator
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_2_TO_3,
            'description' => 'Eerste beoordeling voltooid',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
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
            'id' => self::TRANSITION_STAGE_3_TO_2,
            'description' => 'Uitvoeringscoordinator oneens met eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    3,
                    'implementationCoordinatorAssessment',
                    Operator::Identical,
                    'Oneens met de eerste beoordeling'
                )
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling' en goedgekeurd door de eerste beoordelaar
        // gaat de aanvraag gaat naar de IC
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_3_TO_4,
            'description' => 'Interne beoordeling eens met eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
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
                        'implementationCoordinatorAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling' en 'Afgekeurd', wordt de aanvraag definitief
        // afgekeurd en wordt de afkeuringsbrief verzonden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_3_TO_REJECTED,
            'description' => 'Implementatie coordinator eens met afkeuring',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Rejected,
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
                        'implementationCoordinatorAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => true,
            'assign_to_previous_assessor' => false,
            'clone_data' => false
        ]);


        // Bij een beoordeeloptie 'Oneens met de eerste beoordeling' moet de aanvraag opnieuw
        // volledig beoordeeld worden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_2,
            'description' => 'Interne controleur oneens met eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    4,
                    'internalAssessment',
                    Operator::Identical,
                    'Oneens met de eerste beoordeling'
                )
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling' wordt de aanvraag definitief goedgekeurd en wordt een
        // toekenningsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_APPROVED,
            'description' => 'Interne auditor eens met goedkeuring',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Approved,
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
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => true,
        ]);
    }
}
