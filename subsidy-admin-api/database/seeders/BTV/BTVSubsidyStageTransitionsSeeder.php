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
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT\SubsidyStagesSeeder;

class BTVSubsidyStageTransitionsSeeder extends Seeder
{
    public const TRANSITION_STAGE_1_TO_2 = '7a766078-8b8e-45c8-b04c-4a8de1fae275';
    public const TRANSITION_STAGE_1_TO_2_TIMEOUT = '335b7ffc-b439-40a2-9b71-9b3df210216c';
    public const TRANSITION_STAGE_2_TO_1 = 'fc076d68-f51a-4aa7-b190-be0c584d0fca';
    public const TRANSITION_STAGE_2_TO_3 = '78014bba-1b91-4417-a8b7-cc97014487c8';
    public const TRANSITION_STAGE_3_TO_2 = '79a4eb8b-d42e-4f49-8f96-ff3433fb75c0';
    public const TRANSITION_STAGE_3_TO_4 = '03c4d9ba-6b82-42da-9ac2-2504f9319a91';
    public const TRANSITION_STAGE_4_TO_REJECTED = '3a293e03-1de4-47bf-917b-841b7c0a1fff';
    public const TRANSITION_STAGE_4_TO_2 = '0be7031b-c841-4c27-8104-2d2676d32cff';
    public const TRANSITION_STAGE_4_TO_5 = '16f83400-7ff9-41ce-8ad7-040e316b8cee';
    public const TRANSITION_STAGE_5_TO_5 = '9055e316-e762-4776-b1fc-9e1c0f57c400';
    public const TRANSITION_STAGE_5_TO_6 = 'a1dbb58a-5643-4424-a1be-5839b85980fb';
    public const TRANSITION_STAGE_5_TO_7 = 'ec046c05-8804-4af4-9fbc-4390b2c52bce';
    public const TRANSITION_STAGE_6_TO_7 = 'd336a82a-f7e3-461b-a1cf-ffa7a1d7bf9b';
    public const TRANSITION_STAGE_6_TO_5 = '739aecd5-4c03-424e-bf12-a7f3cecc7d94';
    public const TRANSITION_STAGE_7_TO_8 = 'f59b5798-b2a7-44f0-8301-6c5e50a75194';
    public const TRANSITION_STAGE_7_TO_6 = 'a47e0ae1-74a9-4522-8784-a4ee76e670a6';
    public const TRANSITION_STAGE_8_TO_APPROVED = '22372aab-995d-4f68-a9e2-c899217eac88';
    public const TRANSITION_STAGE_8_TO_RECLAIM = '3ad4bbc8-4810-4d57-a004-f059a930df17';
    public const TRANSITION_STAGE_8_TO_6 = 'cd0491f3-9eef-4094-87fa-ae3babcacd04';

    public const ASCERTAIN_TIMEMOUT_IN_DAYS = 7 * 52;
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
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
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
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
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
            'description' => 'Interne controle is het oneens met de eerste beoordeling',
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
            'description' => 'Interne controle is het eens met de afkeuring',
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
            'id' => self::TRANSITION_STAGE_4_TO_5,
            'description' => 'Interne controle is het eens met de goedkeuring',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
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
                        'internalAssessment',
                        Operator::Identical,
                        'Eens met de eerste beoordeling'
                    )
                ])
            ),
            'send_message' => true,
            'expiration_period' => self::ASCERTAIN_TIMEMOUT_IN_DAYS
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_5_TO_7,
            'description' => 'Voortijdige vaststellings beoordeling voltooid',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            'condition' => $encoder->encode(
                new InCondition(
                    5,
                    'assessment',
                    ['Vaststellen', 'Vorderen']
                )
            ),
            'send_message' => false
        ]);

        // User did not respond in time.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_5_TO_6,
            'description' => 'Vaststellings periode voltooid',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            'condition' => null,
            'send_message' => false,
            'evaluation_trigger' => EvaluationTrigger::Expiration,
        ]);

        // Voortijdige vaststellings beoordeling - 'Uitstellen' wordt de vastelling uitgesteld en is het mogelijk om opnieuw de vastellings beoordeling te doen.
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_5_TO_5,
            'description' => 'Voortijdige vaststelling uitstellen',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    5,
                    'assessment',
                    Operator::Identical,
                    'Uitstellen'
                )
            ),
            'send_message' => false,
            'expiration_period' => self::ASCERTAIN_TIMEMOUT_IN_DAYS,
        ]);

        // Eerste beoordeling = Goedgekeurd of Afgekeurd, aanvraag wordt doorgezet voor de tweede beoordeling
        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_6_TO_7,
            'description' => 'Vaststellings beoordeling voltooid',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            'condition' => $encoder->encode(
                new InCondition(
                    6,
                    'assessment',
                    ['Vaststellen', 'Vorderen']
                )
            ),
            'send_message' => false
        ]);

        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_6_TO_5,
            'description' => 'Vaststelling uitstellen',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            'condition' => $encoder->encode(
                new ComparisonCondition(
                    6,
                    'assessment',
                    Operator::Identical,
                    'Uitstellen'
                )
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true,
            'expiration_period' => self::ASCERTAIN_TIMEMOUT_IN_DAYS
        ]);

        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_7_TO_8,
            'description' => 'Interne controle is het eens met de vaststellingsbeoordeling',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_8_UUID,
            'condition' => $encoder->encode(
                new AndCondition([
                    new OrCondition([
                        new InCondition(
                            5,
                            'assessment',
                            ['Vaststellen', 'Vorderen']
                        ),
                        new InCondition(
                            6,
                            'assessment',
                            ['Vaststellen', 'Vorderen']
                        )
                    ]),
                    new ComparisonCondition(
                        7,
                        'assessment',
                        Operator::Identical,
                        'Eens met de beoordeling op de vaststelling'
                    )
                ])
            ),
            'send_message' => false
        ]);

        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_7_TO_6,
            'description' => 'Interne controle oneens met de vaststellingsbeoordeling',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            'condition' => $encoder->encode(
                new AndCondition([
                    new OrCondition([
                        new InCondition(
                            5,
                            'assessment',
                            ['Vaststellen', 'Vorderen']
                        ),
                        new InCondition(
                            6,
                            'assessment',
                            ['Vaststellen', 'Vorderen']
                        )
                    ]),
                    new ComparisonCondition(
                        7,
                        'assessment',
                        Operator::Identical,
                        'Oneens met de beoordeling op de vaststelling'
                    )
                ])
            ),
            'send_message' => false
        ]);

        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_8_TO_RECLAIM,
            'description' => 'Uitvoeringscoördinator is het eens met de vordering',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_8_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Reclaimed,
            'condition' => $encoder->encode(
                new AndCondition([
                    new OrCondition([
                        new ComparisonCondition(
                            5,
                            'assessment',
                            Operator::Identical,
                            'Vorderen'
                        ),
                        new ComparisonCondition(
                            6,
                            'assessment',
                            Operator::Identical,
                            'Vorderen'
                        ),
                    ]),
                    new ComparisonCondition(
                        7,
                        'assessment',
                        Operator::Identical,
                        'Eens met de beoordeling op de vaststelling'
                    ),
                    new ComparisonCondition(
                        8,
                        'assessment',
                        Operator::Identical,
                        'Eens met de beoordeling op de vaststelling'
                    )
                ])
            ),
            'send_message' => true,
            'assign_to_previous_assessor' => false,
            'clone_data' => false
        ]);

        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_8_TO_APPROVED,
            'description' => 'Uitvoeringscoördinator is het eens met de vaststellingsbeoordeling',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_8_UUID,
            'target_subsidy_stage_id' => null,
            'target_application_status' => ApplicationStatus::Approved,
            'condition' => $encoder->encode(
                new AndCondition([
                    new OrCondition([
                        new ComparisonCondition(
                            5,
                            'assessment',
                            Operator::Identical,
                            'Vaststellen'
                        ),
                        new ComparisonCondition(
                            6,
                            'assessment',
                            Operator::Identical,
                            'Vaststellen'
                        ),
                    ]),
                    new ComparisonCondition(
                        7,
                        'assessment',
                        Operator::Identical,
                        'Eens met de beoordeling op de vaststelling'
                    ),
                    new ComparisonCondition(
                        8,
                        'assessment',
                        Operator::Identical,
                        'Eens met de beoordeling op de vaststelling'
                    )
                ])
            ),
            'send_message' => true,
        ]);

        DB::table('subsidy_stage_transitions')->insert([
            'id' => self::TRANSITION_STAGE_8_TO_6,
            'description' => 'Uitvoeringscoördinator is het oneens met de vaststellingsbeoordeling',
            'current_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_8_UUID,
            'target_subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            'condition' => $encoder->encode(
                new AndCondition([
                    new OrCondition([
                        new ComparisonCondition(
                            5,
                            'assessment',
                            Operator::Identical,
                            'Vaststellen'
                        ),
                        new ComparisonCondition(
                            6,
                            'assessment',
                            Operator::Identical,
                            'Vaststellen'
                        ),
                        new ComparisonCondition(
                            5,
                            'assessment',
                            Operator::Identical,
                            'Vorderen'
                        ),
                        new ComparisonCondition(
                            6,
                            'assessment',
                            Operator::Identical,
                            'Vorderen'
                        ),
                    ]),
                    new ComparisonCondition(
                        7,
                        'assessment',
                        Operator::Identical,
                        'Eens met de beoordeling op de vaststelling'
                    ),
                    new ComparisonCondition(
                        8,
                        'assessment',
                        Operator::Identical,
                        'Oneens met de beoordeling op de vaststelling'
                    )
                ])
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
        ]);
    }
}
