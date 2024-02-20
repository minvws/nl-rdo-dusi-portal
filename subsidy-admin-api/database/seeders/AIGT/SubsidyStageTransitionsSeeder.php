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
    public const TRANSITION_STAGE_3_TO_2 = '04811943-3e98-4532-940f-5b49908a193d';
    public const TRANSITION_STAGE_3_TO_4 = 'd5a683bb-23bc-4c14-8ae2-2b2e62d378bb';
    public const TRANSITION_STAGE_4_TO_2 = '4d3e230b-dec5-4c62-b6d9-8aea62819234';
    public const TRANSITION_STAGE_4_TO_REJECTED = '3063cb42-5d55-4b9b-82e6-6250a4481296';
    public const TRANSITION_STAGE_4_TO_5 = '9fc7740b-1951-4e99-8f89-5608bb0e3a0b';
    public const TRANSITION_STAGE_5_TO_6 = '12c6b427-6dd0-4be1-b7a8-d6ede8ad2485';
    public const TRANSITION_STAGE_6_TO_7 = '7a84de99-53be-4d9c-ba7e-accfc41083e2';
    public const TRANSITION_STAGE_7_TO_8 = 'aa8e5388-af26-4a10-a11e-a59041601df2';
    public const TRANSITION_STAGE_8_TO_APPROVED = '6a4d09fe-c648-45d3-b404-beaa95cb1013';
    public const TRANSITION_STAGE_8_TO_REJECTED = '66d64304-b165-4ada-9cf0-eb28b2772e47';

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

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling' gaat de aanvraag gaat naar de IC
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_3_TO_4,
            'description' => 'Interne beoordeling eens met eerste beoordeling',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            'condition' => $encoder->encode(
                    new ComparisonCondition(
                        3,
                        'internalAssessment',
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
            'description' => 'Implementatie coordinator beoordeling oneens met eerste beoordeling',
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

        // Bij een beoordeeloptie 'Eens met de eerste beoordeling' en 'Afgekeurd', wordt de aanvraag definitief
        // afgekeurd en wordt de afkeuringsbrief verzonden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_REJECTED,
            'description' => 'Implementatie coordinator beoordeling eens met afkeuring',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
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

        // Bij een beoordeeloptie 'Goedgekeurd' wordt de aanvraag definitief goedgekeurd en wordt een
        // toekenningsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_5,
            'description' => 'Implementatie coordinator beoordeling eens met goedkeuring',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_5_UUID,
            'target_application_status' => ApplicationStatus::Allocated,
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
            'send_message' => true,
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_5_TO_6,
            'description' => 'Eerste beoordeling voltooid',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_5_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_6_UUID,
            'condition' => $encoder->encode(
                new InCondition(
                    2,
                    'firstAssessment',
                    ['Goedgekeurd', 'Afgekeurd']
                )
            ),
            'send_message' => false
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_6_TO_7,
            'description' => 'Eerste beoordeling voltooid',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_6_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_7_UUID,
            'condition' => $encoder->encode(
                new InCondition(
                    2,
                    'firstAssessment',
                    ['Goedgekeurd', 'Afgekeurd']
                )
            ),
            'send_message' => false
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_7_TO_8,
            'description' => 'Eerste beoordeling voltooid',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_7_UUID,
            'target_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_8_UUID,
            'condition' => $encoder->encode(
                new InCondition(
                    2,
                    'firstAssessment',
                    ['Goedgekeurd', 'Afgekeurd']
                )
            ),
            'send_message' => false
        ]);


        // Bij een beoordeeloptie 'Eens met de eerste beoordeling' en 'Afgekeurd', wordt de aanvraag definitief
        // afgekeurd en wordt de afkeuringsbrief verzonden.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_8_TO_REJECTED,
            'description' => 'Implementatie coordinator beoordeling eens met afkeuring',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_8_UUID,
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

        // Bij een beoordeeloptie 'Goedgekeurd' wordt de aanvraag definitief goedgekeurd en wordt een
        // toekenningsbrief verzonden
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_8_TO_APPROVED,
            'description' => 'Implementatie coordinator beoordeling eens met goedkeuring',
            'current_subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_8_UUID,
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
                        'implementationCoordinatorAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => true,
        ]);
    }
}
