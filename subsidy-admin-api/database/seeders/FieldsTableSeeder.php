<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;
use Illuminate\Database\Seeder;

class FieldsTableSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        $this->createDateField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'dateOfBirth',
            title: 'Geboortedatum',
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
            title: 'IBAN',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'bankAccountHolder',
            title: 'Naam rekeninghouder',
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
            title: 'Verklaring behandeltraject',
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'proofOfTypeOfMedicalTreatment',
            title: 'Verklaring type behandeling',
        );

        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'permissionToProcessPersonalData',
            title: 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',
        );

        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'truthfullyCompleted',
            title: ''
        );

        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID,
            code: 'checklist',
            title: 'Gecontroleerd',
            options: [
                "Uittreksel van het BRP is opgestuurd",
                "De aanvrager is een ingezetene (> 4 maanden) in Nederland",
                "de aanvrager is ouder dan 18 jaar",
                "De verklaring van de arts over het behandeltraject is opgestuurd",
                "De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud",
                "De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel",
                "Het opgegeven BIG-nummer komt overeen met het BIG-register",
                "De operatie heeft nog niet plaatsgevonden",
                "De aanvrager heeft genderdysforie",
                "De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan",
                "De verklaring van de arts met de vermelding van de type behandeling is opgestuurd",
                "De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel",
                "De type behandeling voldoet aan de voorwaarden conform de subsidieregeling",
                "Het IBAN-nummer klopt met het opgegeven IBAN-nummer van de aanvraag",
                "De tenaamstelling op het bankafschrift of bankpas klopt"
            ]
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID,
            code: 'amount',
            title: 'Bedrag',
            options: [
                "€ 3.830",
                "€ 13.720"
            ]
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID,
            code: 'review',
            title: 'Beoordeling',
            options: [
                "Onbeoordeeld",
                "Aanvulling nodig",
                "Afgekeurd",
                "Goedgekeurd"
            ]
        );
    }
}
