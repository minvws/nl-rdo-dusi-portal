<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;

class SubsidyStagesSeeder extends Seeder
{
    public const STAGE_1_UUID = 'd7f38409-6805-408c-87e9-afd9b00a8de0';
    public const STAGE_2_UUID = 'e1e5d701-f849-4522-b7ca-75bd4785b1f1';
    public const STAGE_3_UUID = '8d206564-6518-4e13-94a4-12c9a3073617';
    public const STAGE_4_UUID = '0200a5c4-70fa-401d-b943-881eb4b877e6';
    public const STAGE_5_UUID = 'e2467684-3d17-4f2b-9d89-274fce583fa7';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_stages')->insert(
            [
                'id' => self::STAGE_1_UUID,
                'subsidy_version_id' => PCZMv2Seeder::VERSION_UUID,
                'title' => 'Aanvraag',
                'subject_role' => SubjectRole::Applicant->value,
                'stage' => 1,
            ]
        );
        DB::table('subsidy_stages')->insert(
            [
                'id' => self::STAGE_2_UUID,
                'subsidy_version_id' => PCZMv2Seeder::VERSION_UUID,
                'title' => 'Eerste beoordeling',
                'subject_role' => SubjectRole::Assessor->value,
                'assessor_user_role' => Role::Assessor->value,
                'stage' => 2,
                'internal_note_field_code' => 'firstAssessmentInternalNote',
            ]
        );
        DB::table('subsidy_stages')->insert(
            [
                'id' => self::STAGE_3_UUID,
                'subsidy_version_id' => PCZMv2Seeder::VERSION_UUID,
                'title' => 'Tweede beoordeling',
                'subject_role' => SubjectRole::Assessor->value,
                'assessor_user_role' => Role::Assessor->value,
                'stage' => 3,
                'internal_note_field_code' => 'secondAssessmentInternalNote',
            ]
        );
        DB::table('subsidy_stages')->insert(
            [
                'id' => self::STAGE_4_UUID,
                'subsidy_version_id' => PCZMv2Seeder::VERSION_UUID,
                'title' => 'Interne controle',
                'subject_role' => SubjectRole::Assessor->value,
                'assessor_user_role' => Role::InternalAuditor->value,
                'stage' => 4,
                'internal_note_field_code' => 'internalAssessmentInternalNote',
            ]
        );
        DB::table('subsidy_stages')->insert(
            [
                'id' => self::STAGE_5_UUID,
                'subsidy_version_id' => PCZMv2Seeder::VERSION_UUID,
                'title' => 'Uitvoeringscoördinator controle',
                'subject_role' => SubjectRole::Assessor->value,
                'assessor_user_role' => Role::ImplementationCoordinator->value,
                'stage' => 5,
                'internal_note_field_code' => 'coordinatorImplementationInternalNote',
            ]
        );
    }
}
