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
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'formOfAddress',
            title: 'Aanspreekvorm',
            options: ['Beste lezer', 'Beste heer', 'Beste mevrouw'],
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'firstName',
            title: 'Voornaam',
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'infix',
            title: 'Tussenvoegsel',
            isRequired: false,
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'lastName',
            title: 'Achternaam',
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'street',
            title: 'Straat',
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'houseNumber',
            title: 'Huisnummer',
            inputMode: 'numeric',
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'houseNumberSuffix',
            title: 'Huisnummer toevoeging',
            maxLength: 10,
            isRequired: false,
        );

        $this->createPostalCodeField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'postalCode',
            title: 'Postcode',
            isRequired: false,
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'city',
            title: 'Plaats',
            maxLength: 100,
        );

        $this->createCountryField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'country',
            title: 'Land',
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'phoneNumber',
            title: 'Telefoonnummer',
            inputMode: 'tel',
            maxLength: 20,
            isRequired: false,
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'email',
            title: 'E-mailadres',
            inputMode: 'email',
            maxLength: 300,
            isRequired: false,
        );

        $this->createBankAccountField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'bankAccountNumber',
            title: 'Rekeningnummer',
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'bankAccountHolder',
            title: 'Tenaamstelling rekeningnummer',
            maxLength: 50,
        );

        $this->createSelectField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'communicationPreference',
            title: 'Communicatievoorkeur',
            options: ['Digitaal', 'Post']
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'bankStatement',
            title: 'Kopie bankafschrift',
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'extractPersonalRecordsDatabase',
            title: 'Uittreksel bevolkingsregister niet ouder dan 3 maanden',
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'proofOfMedicalTreatment',
            title: 'Medische verklaring behandeltraject',
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'proofOfTypeOfMedicalTreatment',
            title: 'Medische verklaring type behandeling',
        );

        $this->createCheckboxField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            code: 'permissionToProcessPersonalData',
            title: 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',
        );
    }
}
