<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;

class SubsidyStagesSeeder extends Seeder
{
    public const SUBSIDY_STAGE_1_UUID = 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1';
    public const SUBSIDY_STAGE_2_UUID = '7075fcad-7d92-42f6-b46c-7733869019e0';
    public const SUBSIDY_STAGE_3_UUID = '0838f8a9-b2ff-4669-9d42-1c51a1134a34';
    public const SUBSIDY_STAGE_4_UUID = 'e5da8f2e-db87-45df-8967-ea3dceb2b207';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_1_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Aanvraag',
            'subject_role' => SubjectRole::Applicant->value,
            'stage' => 1,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_2_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Eerste beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 2,
            'internal_note_field_code' => 'firstAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_3_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Interne controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::InternalAuditor->value,
            'stage' => 3,
            'internal_note_field_code' => 'interalAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_4_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Uitvoeringscoördinator controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::ImplementationCoordinator->value,
            'stage' => 4,
            'internal_note_field_code' => 'implementationCoordinatorAssessmentInternalNote'
        ]);
    }
}
