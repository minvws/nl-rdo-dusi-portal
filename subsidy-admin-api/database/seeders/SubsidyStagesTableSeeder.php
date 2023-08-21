<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubsidyStagesTableSeeder extends Seeder
{
    public const BTV_STAGE_1_UUID = '721c1c28-e674-415f-b1c3-872a631ed045';
    public const BTV_STAGE_2_UUID = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_1_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::BTV_VERSION_UUID,
            'title' => 'Aanvraag',
            'subject_role' => 'applicant',
            'stage' => 1,
            'final_review_deadline' => '2033-12-31 23:59:59',
            'final_review_time_in_s_after_submission' => 604800, // 7 days
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_2_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::BTV_VERSION_UUID,
            'title' => 'Beoordeling',
            'subject_role' => 'assessor',
            'stage' => 2,
            'final_review_deadline' => '2033-12-31 23:59:59',
            'final_review_time_in_s_after_submission' => 604800, // 7 days
        ]);
    }
}
