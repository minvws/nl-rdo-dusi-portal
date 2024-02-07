<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;

class BTVSubsidyStageTransitionsSeeder extends Seeder
{
    public const TRANSITION_STAGE_1_TO_2 = '7a766078-8b8e-45c8-b04c-4a8de1fae275';
    public const TRANSITION_STAGE_2_TO_1 = 'fc076d68-f51a-4aa7-b190-be0c584d0fca';
    public const TRANSITION_STAGE_2_TO_3 = '78014bba-1b91-4417-a8b7-cc97014487c8';
    public const TRANSITION_STAGE_3_TO_2 = '79a4eb8b-d42e-4f49-8f96-ff3433fb75c0';
    public const TRANSITION_STAGE_3_TO_4 = '03c4d9ba-6b82-42da-9ac2-2504f9319a91';
    public const TRANSITION_STAGE_4_TO_APPROVED = '5b876216-ba37-4b13-aa99-e311db027d6b';
    public const TRANSITION_STAGE_4_TO_REJECTED = '3a293e03-1de4-47bf-917b-841b7c0a1fff';
    public const TRANSITION_STAGE_4_TO_2 = '0be7031b-c841-4c27-8104-2d2676d32cff';

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
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            'target_application_status' => ApplicationStatus::Submitted,
            'condition' => null,
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);


        // Eerste beoordeling = Aanvulling nodig; aanvraag wordt teruggezet naar de aanvrager om te laten aanvullen
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_2_TO_1,
            'description' => 'Aanvulling gevraagd',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
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
            'clone_data' => true
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_2_TO_3,
            'description' => 'Eerste beoordeling voltooid',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
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
            'description' => 'Tweede beoordeling oneens met eerste beoordeling',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
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

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling', wordt de aanvraag doorgezet naar de IC.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_3_TO_4,
            'description' => 'Tweede beoordeling eens met eerste beoordeling',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    3,
                    'secondAssessment',
                    Operator::Identical,
                    'Eens met de eerste beoordeling'
                )
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Oneens met de eerste beoordeling' moet de aanvraag opnieuw
        // volledig beoordeeld worden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_2,
            'description' => 'Interne beoordeling oneens met eerste beoordeling',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
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


        // Bij een beoordeeloptie 'Eens met de eerste beoordeling' en 'Afgekeurd', wordt de aanvraag definitief
        // afgekeurd en wordt de afkeuringsbrief verzonden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_REJECTED,
            'description' => 'Interne beoording eens afkeuring',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
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
                        4,
                        'internalAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => true,
            'assign_to_previous_assessor' => false,
            'clone_data' => false
        ]);

        // Bij een beoordeeloptie 'Goedgekeurd' wordt de aanvraag definitief goedgekeurd en wordt een
        // toekenningsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_APPROVED,
            'description' => 'Interne beoordeling eens met goedkeuring',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Approved->value,
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
