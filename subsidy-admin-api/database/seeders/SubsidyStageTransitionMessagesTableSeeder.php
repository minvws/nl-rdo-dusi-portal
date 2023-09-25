<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class SubsidyStageTransitionMessagesTableSeeder extends Seeder
{
    public const PCZM_TRANSITION_STAGE_3_TO_1_MESSAGE = '85bf054e-c6e3-42d2-880d-07c29d0fe6bf';
    public const PZCM_TRANSITION_STAGE_4_TO_REJECTED_MESSAGE = '64a636d8-ed0c-4bb6-982e-f948c68755b6';
    public const PZCM_TRANSITION_STAGE_5_TO_REJECTED_MESSAGE = '7da32b2f-4f0d-44ab-bc87-07718db4bfd5';
    public const PZCM_TRANSITION_STAGE_5_TO_APPROVED_MESSAGE = '9c2ad81e-cf52-41a3-966f-fc9757de15c9';

    public function run(): void
    {
        DB::table('subsidy_stage_transition_messages')->insert([
            'id' => self::PCZM_TRANSITION_STAGE_3_TO_1_MESSAGE,
            'subsidy_stage_transition_id' => SubsidyStageTransitionsTableSeeder::PZCM_TRANSITION_STAGE_3_TO_1,
            'version' => 1,
            'status' => VersionStatus::Published->value,
            'created_at' => Carbon::now(),
            'subject' => 'Aanvulling nodig',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-question-pdf.latte'),
            'content_html' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-question-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_4_TO_REJECTED_MESSAGE,
            'subsidy_stage_transition_id' => SubsidyStageTransitionsTableSeeder::PZCM_TRANSITION_STAGE_4_TO_REJECTED,
            'version' => 1,
            'status' => VersionStatus::Published->value,
            'created_at' => Carbon::now(),
            'subject' => 'Aanvraag afgekeurd',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-rejected-pdf.latte'),
            'content_html' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_5_TO_REJECTED_MESSAGE,
            'subsidy_stage_transition_id' => SubsidyStageTransitionsTableSeeder::PZCM_TRANSITION_STAGE_5_TO_REJECTED,
            'version' => 1,
            'status' => VersionStatus::Published->value,
            'created_at' => Carbon::now(),
            'subject' => 'Aanvraag afgekeurd',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-rejected-pdf.latte'),
            'content_html' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
            'id' => self::PZCM_TRANSITION_STAGE_5_TO_APPROVED_MESSAGE,
            'subsidy_stage_transition_id' => SubsidyStageTransitionsTableSeeder::PZCM_TRANSITION_STAGE_5_TO_APPROVED,
            'version' => 1,
            'status' => VersionStatus::Published->value,
            'created_at' => Carbon::now(),
            'subject' => 'Aanvraag goedgekeurd',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-approved-pdf.latte'),
            'content_html' => file_get_contents(__DIR__ . '/resources/pczm/pczm-letter-approved-view.latte'),
        ]);
    }
}
