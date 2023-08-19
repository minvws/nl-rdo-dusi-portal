<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace Database\Seeders;

use Database\Seeders\Traits\CreateField;
use Illuminate\Database\Seeder;

class FieldsTableSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sort = 0;

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'formOfAddress',
            title: 'Aanspreekvorm',
            options: ['Beste lezer', 'Beste heer', 'Beste mevrouw'],
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'firstName',
            title: 'Voornaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'infix',
            title: 'Tussenvoegsel',
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'lastName',
            title: 'Achternaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'street',
            title: 'Straat',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'houseNumber',
            title: 'Huisnummer',
            inputMode: 'numeric',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'houseNumberSuffix',
            title: 'Huisnummer toevoeging',
            maxLength: 10,
            isRequired: false,
        );

        $this->createPostalCodeField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'postalCode',
            title: 'Postcode',
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'city',
            title: 'Plaats',
            maxLength: 100,
        );

        $this->createCountryField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'country',
            title: 'Land',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'phoneNumber',
            title: 'Telefoonnummer',
            inputMode: 'tel',
            maxLength: 20,
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'email',
            title: 'E-mailadres',
            inputMode: 'email',
            maxLength: 300,
            isRequired: false,
        );

        $this->createBankAccountField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'bankAccountNumber',
            title: 'Rekeningnummer',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'bankAccountHolder',
            title: 'Tenaamstelling rekeningnummer',
            maxLength: 50,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'communicationPreference',
            title: 'Communicatievoorkeur',
            options: ['Digitaal', 'Post']
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'bankStatement',
            title: 'Kopie bankafschrift',
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'extractPersonalRecordsDatabase',
            title: 'Uittreksel bevolkingsregister niet ouder dan 3 maanden',
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'proofOfMedicalTreatment',
            title: 'Medische verklaring behandeltraject',
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'proofOfTypeOfMedicalTreatment',
            title: 'Medische verklaring type behandeling',
        );

        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'permissionToProcessPersonalData',
            title: 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',
        );
    }
}
