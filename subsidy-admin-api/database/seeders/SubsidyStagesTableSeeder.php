<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;

class SubsidyStagesTableSeeder extends Seeder
{
    public const BTV_STAGE_1_UUID = '721c1c28-e674-415f-b1c3-872a631ed045';
    public const BTV_STAGE_2_UUID = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        DB::table('subsidy_stages')->insert([
//            'id' => self::BTV_STAGE_1_UUID,
//            'subsidy_version_id' => SubsidyVersionsTableSeeder::BTV_VERSION_UUID,
//            'title' => 'Aanvraag',
//            'subject_role' => SubjectRole::Applicant->value,
//            'stage' => 1,
//        ]);
//        DB::table('subsidy_stages')->insert([
//            'id' => self::BTV_STAGE_2_UUID,
//            'subsidy_version_id' => SubsidyVersionsTableSeeder::BTV_VERSION_UUID,
//            'title' => 'Beoordeling',
//            'subject_role' => SubjectRole::Assessor->value,
//            'assessor_user_role' => Role::Assessor->value,
//            'stage' => 2,
//        ]);
    }
}
