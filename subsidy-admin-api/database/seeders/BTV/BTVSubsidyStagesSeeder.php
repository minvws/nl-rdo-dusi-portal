<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;

class BTVSubsidyStagesSeeder extends Seeder
{
    public const BTV_STAGE_1_UUID = '721c1c28-e674-415f-b1c3-872a631ed045';
    public const BTV_STAGE_2_UUID = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';
    public const BTV_STAGE_3_UUID = 'b2b08566-8493-4560-8afa-d56402931f74';
    public const BTV_STAGE_4_UUID = 'e456e790-1919-4a2b-b3d5-337d0053abe3';

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
            'title' => 'Interne beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::InternalAuditor->value,
            'stage' => 4,
            'internal_note_field_code' => 'internalAssessmentInternalNote'
        ]);
    }
}
