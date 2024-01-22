<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class BTVSubsidyStageTransitionMessageSeeder extends Seeder
{
    public const TRANSITION_STAGE_3_TO_1_MESSAGE = 'cffe3600-77a9-43b2-9882-7b7f56c4d0ad';
    public const TRANSITION_STAGE_3_TO_REJECTED_MESSAGE = 'c3b32e69-e093-4f0f-9318-7cc771114f2d';
    public const TRANSITION_STAGE_4_TO_REJECTED_MESSAGE = '7476a2bd-15eb-4ab8-be8e-c9f3dd07f9b7';
    public const TRANSITION_STAGE_4_TO_APPROVED_MESSAGE = '1983fa28-cfc6-4c0f-9bc3-cba9e0909456';

    public function run(): void
    {
        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_3_TO_1_MESSAGE,
           'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_2_TO_1,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvulling nodig',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_3_TO_REJECTED_MESSAGE,
           'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_3_TO_REJECTED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag afgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_4_TO_REJECTED_MESSAGE,
           'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_REJECTED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag afgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_4_TO_APPROVED_MESSAGE,
           'subsidy_stage_transition_id' => BTVSubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_APPROVED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag goedgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-view.latte'),
        ]);
    }
}
