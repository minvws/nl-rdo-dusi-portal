<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT\SubsidyStageTransitionsSeeder;

class BTVSubsidyStageTransitionMessageSeeder extends Seeder
{
    public const TRANSITION_STAGE_2_TO_1_MESSAGE = 'cffe3600-77a9-43b2-9882-7b7f56c4d0ad';
    public const TRANSITION_STAGE_4_TO_REJECTED_MESSAGE = 'c3b32e69-e093-4f0f-9318-7cc771114f2d';
    public const TRANSITION_STAGE_4_TO_5_ALLOCATED_MESSAGE = '532d7372-a029-4190-bf8f-c8417ce9acb4';
    public const TRANSITION_STAGE_8_TO_APPROVED_MESSAGE = 'd8c2a8d1-e512-40a1-94f8-6535cc85289c';
    public const TRANSITION_STAGE_8_TO_RECLAIM_MESSAGE = 'd9917011-3baf-4a5f-8b1f-0e8e2b62d0a4';

    public function run(): void
    {
        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_2_TO_1_MESSAGE,
           'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_2_TO_1,
           'version' => 1,
           'status' => VersionStatus::Published,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvulling nodig',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_4_TO_REJECTED_MESSAGE,
           'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_REJECTED,
           'version' => 1,
           'status' => VersionStatus::Published,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag afgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
            'id' => self::TRANSITION_STAGE_4_TO_5_ALLOCATED_MESSAGE,
            'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_5,
            'version' => 1,
            'status' => VersionStatus::Published,
            'created_at' => Carbon::now(),
            'subject' => 'Aanvraag goedgekeurd',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-allocated-pdf.latte'),
            'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-allocated-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
            'id' => self::TRANSITION_STAGE_8_TO_APPROVED_MESSAGE,
            'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_8_TO_APPROVED,
            'version' => 1,
            'status' => VersionStatus::Published,
            'created_at' => Carbon::now(),
            'subject' => 'Aanvraag vastgesteld',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-pdf.latte'),
            'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
            'id' => self::TRANSITION_STAGE_8_TO_RECLAIM_MESSAGE,
            'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_8_TO_RECLAIM,
            'version' => 1,
            'status' => VersionStatus::Published,
            'created_at' => Carbon::now(),
            'subject' => 'Vordering aanvraag',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-reclaimed-pdf.latte'),
            'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-reclaimed-view.latte'),
        ]);
    }
}
