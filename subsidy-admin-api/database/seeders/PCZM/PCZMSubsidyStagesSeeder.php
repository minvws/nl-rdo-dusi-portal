<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;

class PCZMSubsidyStagesSeeder extends Seeder
{
    public const PCZM_STAGE_1_UUID = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
    public const PCZM_STAGE_2_UUID = '8027c102-93ef-4735-ab66-97aa63b836eb';
    public const PCZM_STAGE_3_UUID = '61436439-E337-4986-BC18-57138E2FAB65';
    public const PCZM_STAGE_4_UUID = '7CEB3C91-5C3B-4627-B9EF-A46D5FE2ED68';
    public const PCZM_STAGE_5_UUID = '85ED726E-CDBE-444E-8D12-C56F9BED2621';
    public const PCZM_STAGE_6_UUID = 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_1_UUID,
            'subsidy_version_id' => PCZMSeeder::PCZM_VERSION_UUID,
            'title' => 'Aanvraag',
            'subject_role' => SubjectRole::Applicant->value,
            'stage' => 1,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_2_UUID,
            'subsidy_version_id' => PCZMSeeder::PCZM_VERSION_UUID,
            'title' => 'Eerste beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 2,
            'internal_note_field_code' => 'firstAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_3_UUID,
            'subsidy_version_id' => PCZMSeeder::PCZM_VERSION_UUID,
            'title' => 'Tweede beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 3,
            'internal_note_field_code' => 'secondAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_4_UUID,
            'subsidy_version_id' => PCZMSeeder::PCZM_VERSION_UUID,
            'title' => 'Interne controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::InternalAuditor->value,
            'stage' => 4,
            'internal_note_field_code' => 'internalAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_5_UUID,
            'subsidy_version_id' => PCZMSeeder::PCZM_VERSION_UUID,
            'title' => 'Uitvoeringscoördinator controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::ImplementationCoordinator->value,
            'stage' => 5,
            'internal_note_field_code' => 'coordinatorImplementationInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::PCZM_STAGE_6_UUID,
            'subsidy_version_id' => PCZMSeeder::PCZM_VERSION_UUID,
            'title' => 'Informeren over verhoging van toegekend bedrag',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::ImplementationCoordinator->value,
            'stage' => 6,
        ]);
    }
}
