<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class PCZMSubsidyStageTransitionMessageSeeder extends Seeder
{
    public const PCZM_TRANSITION_STAGE_2_TO_1_MESSAGE = '85bf054e-c6e3-42d2-880d-07c29d0fe6bf';
    public const PZCM_TRANSITION_STAGE_3_TO_REJECTED_MESSAGE = '64a636d8-ed0c-4bb6-982e-f948c68755b6';
    public const PZCM_TRANSITION_STAGE_5_TO_REJECTED_MESSAGE = '7da32b2f-4f0d-44ab-bc87-07718db4bfd5';
    public const PZCM_TRANSITION_STAGE_5_TO_APPROVED_MESSAGE = '9c2ad81e-cf52-41a3-966f-fc9757de15c9';
    public const PZCM_TRANSITION_STAGE_6_TO_INCREASED_MESSAGE = 'd3dcc915-fdaf-472a-9f3c-d9a09dc263b3';

    public function run(): void
    {
        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::PCZM_TRANSITION_STAGE_2_TO_1_MESSAGE,
           'subsidy_stage_transition_id' => PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_2_TO_1,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvulling nodig',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::PZCM_TRANSITION_STAGE_3_TO_REJECTED_MESSAGE,
           'subsidy_stage_transition_id' => PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_3_TO_REJECTED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag afgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::PZCM_TRANSITION_STAGE_5_TO_REJECTED_MESSAGE,
           'subsidy_stage_transition_id' => PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_5_TO_REJECTED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag afgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::PZCM_TRANSITION_STAGE_5_TO_APPROVED_MESSAGE,
           'subsidy_stage_transition_id' => PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_5_TO_APPROVED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag goedgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::PZCM_TRANSITION_STAGE_6_TO_INCREASED_MESSAGE,
           'subsidy_stage_transition_id' => PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_6_TO_INCREASE_EMAIL,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Verhoging toegewezen bedrag',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-increased-amount-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-increased-amount-view.latte'),
        ]);
    }
}
