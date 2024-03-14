<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class SubsidyStageTransitionMessageSeeder extends Seeder
{
    public const TRANSITION_STAGE_2_TO_1_MESSAGE = 'a9ed4e8e-932e-43cf-afb6-364ef54403e6';
    public const TRANSITION_STAGE_4_TO_REJECTED_MESSAGE = '350d6eae-0f5e-49aa-9c80-280bcc6efafb';
    public const TRANSITION_STAGE_4_TO_APPROVED_MESSAGE = '9445db1e-2aeb-4434-be02-e57622c28e77';

    public function run(): void
    {
        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_2_TO_1_MESSAGE,
           'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_2_TO_1,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvulling nodig',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_4_TO_REJECTED_MESSAGE,
           'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_REJECTED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag afgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
        ]);

        DB::table('subsidy_stage_transition_messages')->insert([
           'id' => self::TRANSITION_STAGE_4_TO_APPROVED_MESSAGE,
           'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_APPROVED,
           'version' => 1,
           'status' => VersionStatus::Published->value,
           'created_at' => Carbon::now(),
           'subject' => 'Aanvraag goedgekeurd',
           'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-pdf.latte'),
           'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-view.latte'),
        ]);
    }
}
