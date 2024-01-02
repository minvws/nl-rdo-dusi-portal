<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class BTVApplicationFieldsSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createBTVFields();
    }

    public function createBTVFields(): void
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
            code:           'bankStatement',
            title:          'Kopie bankafschrift',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    5242880
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'extractPopulationRegisterDocument',
            title:          'Uittreksel bevolkingsregister niet ouder dan 3 maanden',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    5242880
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'proofOfMedicalTreatmentDocument',
            title:          'Verklaring behandeltraject',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    5242880
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'proofOfTypeOfMedicalTreatmentDocument',
            title:          'Verklaring type behandeling',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    5242880
        );

        $this->createCheckboxField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'permissionToProcessPersonalData',
            title:          'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',
        );

        $this->createCheckboxField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            code:           'truthfullyCompleted',
            title:          ''
        );

    }
}
