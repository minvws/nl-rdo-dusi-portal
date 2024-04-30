<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT\AIGTSeeder;

class BTVSubsidyStagesSeeder extends Seeder
{
    public const BTV_STAGE_1_UUID = '721c1c28-e674-415f-b1c3-872a631ed045';
    public const BTV_STAGE_2_UUID = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';
    public const BTV_STAGE_3_UUID = 'b2b08566-8493-4560-8afa-d56402931f74';
    public const BTV_STAGE_4_UUID = 'e456e790-1919-4a2b-b3d5-337d0053abe3';
    public const BTV_STAGE_5_UUID = '1ec333d3-4b9c-437f-a04d-c1f6a7b70446';
    public const BTV_STAGE_6_UUID = 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82';
    public const BTV_STAGE_7_UUID = '0c2c1f22-624c-45fc-bb20-a3249b647fa7';
    public const BTV_STAGE_8_UUID = '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_1_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Aanvraag',
            'subject_role' => SubjectRole::Applicant->value,
            'stage' => 1,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_2_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Eerste beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 2,
            'internal_note_field_code' => 'firstAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_3_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Tweede beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 3,
            'internal_note_field_code' => 'secondAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_4_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Interne Controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::InternalAuditor->value,
            'stage' => 4,
            'internal_note_field_code' => 'internalAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_5_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Wachten op vaststelling',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::Assessor,
            'stage' => 5,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_6_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Vaststellings controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::Assessor,
            'stage' => 6,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_7_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Interne vaststellings controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::InternalAuditor,
            'stage' => 7,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::BTV_STAGE_8_UUID,
            'subsidy_version_id' => BTVSeeder::BTV_VERSION_UUID,
            'title' => 'Uitvoeringscoördinator vaststellings controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::ImplementationCoordinator,
            'stage' => 8,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
    }
}
