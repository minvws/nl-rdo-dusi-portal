<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;

class SubsidyStagesTableSeeder extends Seeder
{
    public const BTV_STAGE_1_UUID = '721c1c28-e674-415f-b1c3-872a631ed045';
    public const BTV_STAGE_2_UUID = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

    public const PCZM_STAGE_1_UUID = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
    public const PCZM_STAGE_2_UUID = '8027c102-93ef-4735-ab66-97aa63b836eb';
    public const PCZM_STAGE_3_UUID = '61436439-E337-4986-BC18-57138E2FAB65';
    public const PCZM_STAGE_4_UUID = '7CEB3C91-5C3B-4627-B9EF-A46D5FE2ED68';
    public const PCZM_STAGE_5_UUID = '85ED726E-CDBE-444E-8D12-C56F9BED2621';

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
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_1_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::PCZM_VERSION_UUID,
            'title' => 'Aanvraag',
            'subject_role' => SubjectRole::Applicant->value,
            'stage' => 1,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_2_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::PCZM_VERSION_UUID,
            'title' => 'Eerste beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 2,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_3_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::PCZM_VERSION_UUID,
            'title' => 'Tweede beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 3,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_4_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::PCZM_VERSION_UUID,
            'title' => 'Interne controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::InternalAuditor->value,
            'stage' => 4,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_5_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::PCZM_VERSION_UUID,
            'title' => 'Uitvoeringscoördinator controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::ImplementationCoordinator->value,
            'stage' => 5,
        ]);
    }
}
