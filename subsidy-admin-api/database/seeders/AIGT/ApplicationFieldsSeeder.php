<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class ApplicationFieldsSeeder extends Seeder
{
    use CreateField;

    public const SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID = 'c47536b4-b44a-4621-b677-f61ce34997d5';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createFields();
        $this->createSubsidyStageHashes();
    }

    public function createFields(): void
    {
        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'firstName',
            title:          'Voornaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'infix',
            title:          'Tussenvoegsel',
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'lastName',
            title:          'Achternaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'street',
            title:          'Straat',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'houseNumber',
            title:          'Huisnummer',
            inputMode:      'numeric',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'houseNumberSuffix',
            title:          'Huisnummer toevoeging',
            params:         ['maxLength' => 10],
            isRequired:     false,
        );

        $this->createPostalCodeField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'postalCode',
            title:          'Postcode',
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'city',
            title:          'Plaats',
            params:         ['maxLength' => 100],
        );

        $this->createCountryField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'country',
            title:          'Land',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'phoneNumber',
            title:          'Telefoonnummer',
            inputMode:      'tel',
            params:         ['maxLength' => 20],
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'email',
            title:          'E-mailadres',
            inputMode:      'email',
            params:         ['maxLength' => 300],
            isRequired:     false,
        );

        $this->createBankAccountField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'bankAccountNumber',
            title:          'IBAN',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'bankAccountHolder',
            title:          'Naam rekeninghouder',
            params:         ['maxLength' => 50],
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code: 'bankStatement',
            title: 'Bankafschrift',
            isRequired: false,
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createDateField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'abroadCourseComponentStartDate',
            title:          'Start opleidingsonderdeel buitenland',
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'proofOfRegistrationDocument',
            title:          'Bewijs van inschrijving',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'educationalInstituteDeclarationDocument',
            title:          'Verklaring van opleidingsinstituut',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'truthfullyCompleted',
            title:          '',
            excludeFromCloneData: true
        );
    }

    private function createSubsidyStageHashes(): void
    {
        DB::table('subsidy_stage_hashes')->insert([
            'id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            'name' => 'Bank account',
            'description' => 'Bank account duplicate reporting',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var Field $bankAccountNumber */
        $bankAccountNumber = DB::table('fields')
            ->where('subsidy_stage_id', SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID)
            ->where('code', 'bankAccountNumber')
            ->where('title', 'IBAN')
            ->first();

        DB::table('subsidy_stage_hash_fields')->insert([
            'subsidy_stage_hash_id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'field_id' => $bankAccountNumber->id,
        ]);
    }
}
