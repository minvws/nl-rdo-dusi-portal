<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class SubsidyStageTransitionMessageSeeder extends Seeder
{
    public const TRANSITION_STAGE_2_TO_1_MESSAGE = 'c6410597-cbc0-45f4-aa0c-3d8631d661f2';
    public const TRANSITION_STAGE_4_TO_REJECTED_MESSAGE = 'b135a0f1-c584-4f69-bbad-e9db91a0de6d';
    public const TRANSITION_STAGE_4_TO_APPROVED_MESSAGE = 'ef41a929-6556-4dec-975e-5d75f5a48a64';

    public function run(): void
    {
        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_2_TO_1_MESSAGE,
           'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_2_TO_1,
           'version' => 1,
           'status' => VersionStatus::Published,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvulling nodig',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_4_TO_REJECTED_MESSAGE,
           'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_REJECTED,
           'version' => 1,
           'status' => VersionStatus::Published,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag afgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_4_TO_APPROVED_MESSAGE,
           'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_APPROVED,
           'version' => 1,
           'status' => VersionStatus::Published,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag goedgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-view.latte'),
        ]);
    }
}
