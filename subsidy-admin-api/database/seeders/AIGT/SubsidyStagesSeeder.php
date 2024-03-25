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
    public const SUBSIDY_STAGE_5_UUID = '59ddbc42-8ffc-4e2c-a751-d937714b6df6';
    public const SUBSIDY_STAGE_6_UUID = '2b06aee1-ea36-41a4-b7ae-74fa53c64a64';
    public const SUBSIDY_STAGE_7_UUID = 'dfd0310d-3bf6-4e38-a2d5-0a3223ac20c8';
    public const SUBSIDY_STAGE_8_UUID = '051364be-fa12-4af7-a1b8-c80f5e9dd652';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_1_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Aanvraag',
            'subject_role' => SubjectRole::Applicant,
            'stage' => 1,
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_2_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Eerste beoordeling',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::Assessor,
            'stage' => 2,
            'internal_note_field_code' => 'firstAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_3_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Interne controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::InternalAuditor,
            'stage' => 3,
            'internal_note_field_code' => 'internalAssessmentInternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_4_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Uitvoeringscoördinator controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::ImplementationCoordinator,
            'stage' => 4,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_5_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Wachten op vaststelling',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::Assessor,
            'stage' => 5,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_6_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Vaststellings controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::Assessor,
            'stage' => 6,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_7_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Interne vaststellings controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::InternalAuditor,
            'stage' => 7,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
        DB::table('subsidy_stages')->insert([
            'id' => self::SUBSIDY_STAGE_8_UUID,
            'subsidy_version_id' => AIGTSeeder::SUBSIDY_VERSION_UUID,
            'title' => 'Uitvoeringscoördinator vaststellings controle',
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => Role::ImplementationCoordinator,
            'stage' => 8,
            'allow_duplicate_assessors' => true,
            'internal_note_field_code' => 'InternalNote'
        ]);
    }
}
