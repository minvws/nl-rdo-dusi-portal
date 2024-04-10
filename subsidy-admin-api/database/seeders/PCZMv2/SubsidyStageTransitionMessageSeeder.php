<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class SubsidyStageTransitionMessageSeeder extends Seeder
{
    public const TRANSITION_STAGE_2_TO_1_MESSAGE = '961023c7-82a9-40bb-b6b7-cd3882220681';
    public const TRANSITION_STAGE_5_TO_REJECTED_MESSAGE = '56f521d5-c079-4849-a06e-5bd1e672042b';
    public const TRANSITION_STAGE_5_TO_APPROVED_MESSAGE = '8908ef68-5241-4b4e-961f-304b53f3695c';

    public function run(): void
    {
        DB::table('subsidy_stage_transition_messages')->insert(
            [
                'id' => self::TRANSITION_STAGE_2_TO_1_MESSAGE,
                'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_2_TO_1,
                'version' => 1,
                'status' => VersionStatus::Published->value,
                'created_at' => Carbon::now(),
                'subject' => 'Aanvulling nodig',
                'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-pdf.latte'),
                'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-requestForChanges-view.latte'),
            ]
        );

        DB::table('subsidy_stage_transition_messages')->insert(
            [
                'id' => self::TRANSITION_STAGE_5_TO_REJECTED_MESSAGE,
                'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_5_TO_REJECTED,
                'version' => 1,
                'status' => VersionStatus::Published->value,
                'created_at' => Carbon::now(),
                'subject' => 'Aanvraag afgekeurd',
                'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-pdf.latte'),
                'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-rejected-view.latte'),
            ]
        );

        DB::table('subsidy_stage_transition_messages')->insert(
            [
                'id' => self::TRANSITION_STAGE_5_TO_APPROVED_MESSAGE,
                'subsidy_stage_transition_id' => SubsidyStageTransitionsSeeder::TRANSITION_STAGE_5_TO_APPROVED,
                'version' => 1,
                'status' => VersionStatus::Published->value,
                'created_at' => Carbon::now(),
                'subject' => 'Aanvraag goedgekeurd',
                'content_pdf' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-pdf.latte'),
                'content_html' => file_get_contents(__DIR__ . '/resources/letters/letter-approved-view.latte'),
            ]
        );
    }
}
