<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM\PCZMSubsidyStagesSeeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class BTVApplicationFieldsSeeder extends Seeder
{
    use CreateField;

    public const SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID = '70609201-1301-455c-942b-654236221970';

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
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'firstName',
            title:          'Voornaam',
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'infix',
            title:          'Tussenvoegsel',
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'lastName',
            title:          'Achternaam',
        );

        $this->createDateField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'dateOfBirth',
            title:          'Geboortedatum',
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'street',
            title:          'Straat',
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'houseNumber',
            title:          'Huisnummer',
            inputMode:      'numeric',
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'houseNumberSuffix',
            title:          'Huisnummer toevoeging',
            maxLength:      10,
            isRequired:     false,
        );

        $this->createPostalCodeField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'postalCode',
            title:          'Postcode',
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'city',
            title:          'Plaats',
            maxLength:      100,
        );

        $this->createCountryField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'country',
            title:          'Land',
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'phoneNumber',
            title:          'Telefoonnummer',
            inputMode:      'tel',
            maxLength:      20,
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'email',
            title:          'E-mailadres',
            inputMode:      'email',
            maxLength:      300,
            isRequired:     false,
        );

        $this->createBankAccountField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'bankAccountNumber',
            title:          'IBAN',
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'bankAccountHolder',
            title:          'Naam rekeninghouder',
            maxLength:      50,
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'extractPopulationRegisterDocument',
            title:          'Uittreksel bevolkingsregister',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'proofOfMedicalTreatmentDocument',
            title:          'Verklaring behandeltraject',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'proofOfTypeOfMedicalTreatmentDocument',
            title:          'Verklaring type behandeling',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createCheckboxField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'truthfullyCompleted',
            title:          '',
            exclude_from_clone_data: true
        );

    }

    private function createSubsidyStageHashes(): void
    {
        DB::table('subsidy_stage_hashes')->insert([
            'id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            'name' => 'Bank account',
            'description' => 'Bank account duplicate reporting',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var Field $bankAccountNumber */
        $bankAccountNumber = DB::table('fields')
            ->where('subsidy_stage_id', BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID)
            ->where('code', 'bankAccountNumber')
            ->where('title', 'IBAN')
            ->first();

        DB::table('subsidy_stage_hash_fields')->insert([
            'subsidy_stage_hash_id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'field_id' => $bankAccountNumber->id,
        ]);
    }
}
