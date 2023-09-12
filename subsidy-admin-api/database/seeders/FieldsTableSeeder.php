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
        $this->createBTVFields();
        $this->createPCZMFields();
    }

    public function createBTVFields(): void
    {
        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'firstName', title: 'Voornaam',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'infix', title: 'Tussenvoegsel', isRequired: false,);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'lastName', title: 'Achternaam',);

        $this->createDateField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'dateOfBirth', title: 'Geboortedatum',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'street', title: 'Straat',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'houseNumber', title: 'Huisnummer', inputMode: 'numeric',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'houseNumberSuffix', title: 'Huisnummer toevoeging', maxLength: 10, isRequired: false,);

        $this->createPostalCodeField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'postalCode', title: 'Postcode', isRequired: false,);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'city', title: 'Plaats', maxLength: 100,);

        $this->createCountryField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'country', title: 'Land',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'phoneNumber', title: 'Telefoonnummer', inputMode: 'tel', maxLength: 20, isRequired: false,);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'email', title: 'E-mailadres', inputMode: 'email', maxLength: 300, isRequired: false,);

        $this->createBankAccountField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'bankAccountNumber', title: 'IBAN',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'bankAccountHolder', title: 'Naam rekeninghouder', maxLength: 50,);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'bankStatement', title: 'Kopie bankafschrift',);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'extractPersonalRecordsDatabase', title: 'Uittreksel bevolkingsregister niet ouder dan 3 maanden',);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'proofOfMedicalTreatment', title: 'Verklaring behandeltraject',);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'proofOfTypeOfMedicalTreatment', title: 'Verklaring type behandeling',);

        $this->createCheckboxField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'permissionToProcessPersonalData', title: 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',);

        $this->createCheckboxField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID, code: 'truthfullyCompleted', title: '');

        $this->createMultiSelectField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID, code: 'checklist', title: 'Gecontroleerd', options: [
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
            ]);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID, code: 'amount', title: 'Bedrag', options: [
                "€ 3.830",
                "€ 13.720"
            ]);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID, code: 'review', title: 'Beoordeling', options: [
                "Onbeoordeeld",
                "Aanvulling nodig",
                "Afgekeurd",
                "Goedgekeurd"
            ]);
    }

    public function createPCZMFields(): void
    {
        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'firstName', title: 'Voornaam',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'infix', title: 'Tussenvoegsel', isRequired: false,);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'lastName', title: 'Achternaam',);

        $this->createDateField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'dateOfBirth', title: 'Geboortedatum',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'street', title: 'Straat',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'houseNumber', title: 'Huisnummer', inputMode: 'numeric',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'houseNumberSuffix', title: 'Huisnummer toevoeging', maxLength: 10, isRequired: false,);

        $this->createPostalCodeField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'postalCode', title: 'Postcode', isRequired: false,);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'city', title: 'Plaats', maxLength: 100,);

        $this->createCountryField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'country', title: 'Land',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'phoneNumber', title: 'Telefoonnummer', inputMode: 'tel', maxLength: 20, isRequired: false,);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'email', title: 'E-mailadres', inputMode: 'email', maxLength: 300, isRequired: false,);

        $this->createBankAccountField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'bankAccountNumber', title: 'IBAN',);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'bankAccountHolder', title: 'Naam rekeninghouder', maxLength: 50,);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'certifiedEmploymentDocument', title: 'Gewaarmerkt verzekeringsbericht',);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'isWiaDecisionPostponed', title: 'Is WIA beslissing uitgesteld?', options: [
            "Ja",
            "Nee",
        ]);

        //If isWiaDecisionPostponed === yes
        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'wiaDecisionPostponedLetter', title: 'Toekenningsbrief',);

        //If isWiaDecisionPostponed === no
        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'wiaDecisionDocument', title: 'WIA-Beslissing',);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'employmentContract', title: 'Arbeidsovereenkomst',);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'employmentFunction', title: 'Functie', options: [
                "Ambulancechauffeur",
                "Anesthesiemedewerker en/of operatieassistent",
                "Arts",
                "Bachelor medisch hulpverlener",
                "Doktersassistent",
                "Helpende",
                "Physician assistant",
                "Praktijkondersteuner huisarts",
                "Verpleegkundig specialist",
                "(gespecialiseerd) Verpleegkundige",
                "Verzorgende in de individuele gezondheidszorg (VIG’er) of verzorgende",
                "Zorgondersteuner en/of voedingsassistent",
                "Anders",
            ]);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'otherEmploymentFunction', title: 'Andere functie', maxLength: 300,);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'employerKind', title: 'Werkgever', options: [
            "Zorgaanbieder",
            "Andere organisatie",
        ]);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'otherEmployerDeclarationFile', title: 'Zorgaanbiedersverklaring',);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'hasBeenWorkingAtJudicialInstitution', title: 'Bent u werkzaamh geweest bij een justitiële inrichting?', options: [
            "Ja",
            "Nee",
        ]);

        $this->createTextField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'BIGNumber', title: 'BIG-nummer', maxLength: 11,);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'socialMedicalAssessment', title: 'Sociaal-medische beoordeling',);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'hasPostCovidComplaints', title: 'Heeft langdurige post-COVID klachten', options: [
            "Ja",
            "Nee",
        ]);

        $this->createUploadField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'doctorsCertificate', title: 'Verklaring arts',);

        $this->createCheckboxField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'permissionToProcessPersonalData', title: 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',);

        $this->createCheckboxField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID, code: 'truthfullyCompleted', title: '');

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID, code: 'amount', title: 'Bedrag', options: [
            "€ 15.000"
        ]);

        $this->createSelectField(subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID, code: 'review', title: 'Beoordeling', options: [
                "Onbeoordeeld",
                "Aanvulling nodig",
                "Afgekeurd",
                "Goedgekeurd"
            ]);
    }
}
