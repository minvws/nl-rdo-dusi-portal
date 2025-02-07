<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;

class SubsidyStagesSeeder extends Seeder
{
    public const SUBSIDY_STAGE_1_UUID = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8';
    public const SUBSIDY_STAGE_2_UUID = 'fb21ee98-9f58-40b1-9432-fad2937688dc';
    public const SUBSIDY_STAGE_3_UUID = 'f343892a-17a8-48e5-81b0-6c3cb710c29a';
    public const SUBSIDY_STAGE_4_UUID = 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_1_UUID,
            'subsidy_version_id' => DAMUSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Aanvraag',
            'subject_role' => SubjectRole::Applicant->value,
            'stage' => 1,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_2_UUID,
            'subsidy_version_id' => DAMUSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Eerste beoordeling',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::Assessor->value,
            'stage' => 2,
            'internal_note_field_code' => 'firstAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_3_UUID,
            'subsidy_version_id' => DAMUSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Uitvoeringscoördinator controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::ImplementationCoordinator->value,
            'stage' => 3,
            'internal_note_field_code' => 'implementationCoordinatorAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_4_UUID,
            'subsidy_version_id' => DAMUSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Interne controle',
            'subject_role' => SubjectRole::Assessor->value,
            'assessor_user_role' => Role::InternalAuditor->value,
            'stage' => 4,
            'internal_note_field_code' => 'internalAssessmentInternalNote'
        ]);
    }
}
