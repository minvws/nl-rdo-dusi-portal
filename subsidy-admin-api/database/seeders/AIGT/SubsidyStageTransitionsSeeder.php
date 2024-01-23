<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;

class SubsidyStageTransitionsSeeder extends Seeder
{
    public const TRANSITION_STAGE_1_TO_2 = '24a47df1-fc9d-4557-9012-d51738e5bdec';
    public const TRANSITION_STAGE_2_TO_1 = '2f2e080d-0a05-467a-aaa5-292a95a6d361';
    public const TRANSITION_STAGE_2_TO_3 = '38957187-d17f-4e77-b4b2-90797f76b521';
    public const TRANSITION_STAGE_3_TO_REJECTED = '4d3e230b-dec5-4c62-b6d9-8aea62819234';
    public const TRANSITION_STAGE_3_TO_2 = '04811943-3e98-4532-940f-5b49908a193d';
    public const TRANSITION_STAGE_3_TO_4 = 'd5a683bb-23bc-4c14-8ae2-2b2e62d378bb';
    public const TRANSITION_STAGE_4_TO_APPROVED = '72bc33b6-2fbe-4d05-bd3b-0e9e88adb76a';
    public const TRANSITION_STAGE_4_TO_REJECTED = 'e4eb01fb-2acf-469c-9ffe-9a0a8be04752';

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
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
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
            'description' => 'Interne beoordeling oneens met eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    3,
                    'internalAssessment',
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
            'id' => self::TRANSITION_STAGE_3_TO_REJECTED,
            'description' => 'Interne beoordeling eens met afkeuring eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
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
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => true
        ]);

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling', wordt de aanvraag als volgt doorgezet:
        // - Eerste beoordeling = goedgekeurd; aanvraag gaat naar de IC
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_3_TO_4,
            'description' => 'Interne beoordeling eens met goedkeuring eerste beoordeling',
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
                        3,
                        'internalAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => false
        ]);

        // Bij een beoordeeloptie 'Oneens met de eerste beoordeling' gaat de aanvraag terug naar de eerste beoordelaar
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_REJECTED,
            'description' => 'Interne beoording oneens met eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    4,
                    'implementationCoordinatorAssessment',
                    Operator::Identical,
                    'Oneens met de eerste beoordeling'
                )
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);

        // Bij een beoordeeloptie 'Goedgekeurd' wordt de aanvraag definitief goedgekeurd en wordt een
        // toekenningsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_APPROVED,
            'description' => 'Interne beoordeling eens met eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Approved->value,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    4,
                    'implementationCoordinatorAssessment',
                    Operator::Identical,
                    'Eens met de eerste beoordeling'
                )
            ),
            'send_message' => true
        ]);
    }
}
