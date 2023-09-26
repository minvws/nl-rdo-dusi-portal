<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

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

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'bankStatement',
            title: 'Kopie bankafschrift',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 5242880
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'extractPersonalRecordsDatabase',
            title: 'Uittreksel bevolkingsregister niet ouder dan 3 maanden',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 5242880
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'proofOfMedicalTreatment',
            title: 'Verklaring behandeltraject',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 5242880
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_1_UUID,
            code: 'proofOfTypeOfMedicalTreatment',
            title: 'Verklaring type behandeling',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 5242880
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
                'Uittreksel van het BRP is opgestuurd',
                'De aanvrager is een ingezetene (> 4 maanden) in Nederland',
                'de aanvrager is ouder dan 18 jaar',
                'De verklaring van de arts over het behandeltraject is opgestuurd',
                'De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud',
                'De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel',
                'Het opgegeven BIG-nummer komt overeen met het BIG-register',
                'De operatie heeft nog niet plaatsgevonden',
                'De aanvrager heeft genderdysforie',
                'De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan',
                'De verklaring van de arts met de vermelding van de type behandeling is opgestuurd',
                'De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel',
                'De type behandeling voldoet aan de voorwaarden conform de subsidieregeling',
                'Het IBAN-nummer klopt met het opgegeven IBAN-nummer van de aanvraag',
                'De tenaamstelling op het bankafschrift of bankpas klopt'
            ],
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID,
            code: 'amount',
            title: 'Bedrag',
            options: ['€ 3.830', '€ 13.720']
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::BTV_STAGE_2_UUID,
            code: 'review',
            title: 'Beoordeling',
            options: ['Onbeoordeeld', 'Aanvulling nodig', 'Afgekeurd', 'Goedgekeurd']
        );
    }

    public function createPCZMFields(): void
    {
        $this->pczmApplicationFields();
        $this->pczmFirstAssessmentFields();
        $this->pczmSeconcAssessmentFields();
        $this->pczmInternalAssessmentFields();
        $this->pczmCoordinatorImplemenationFields();
    }


    public function pczmApplicationFields(): void
    {
        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'firstName',
            title: 'Voornaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'infix',
            title: 'Tussenvoegsel',
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'lastName',
            title: 'Achternaam',
        );

        $this->createDateField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'dateOfBirth',
            title: 'Geboortedatum',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'street',
            title: 'Straat',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'houseNumber',
            title: 'Huisnummer',
            inputMode: 'numeric',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'houseNumberSuffix',
            title: 'Huisnummer toevoeging',
            maxLength: 10,
            isRequired: false,
        );

        $this->createPostalCodeField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'postalCode',
            title: 'Postcode',
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'city',
            title: 'Plaats',
            maxLength: 100,
        );

        $this->createCountryField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'country',
            title: 'Land',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'phoneNumber',
            title: 'Telefoonnummer',
            inputMode: 'tel',
            maxLength: 20,
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'email',
            title: 'E-mailadres',
            inputMode: 'email',
            maxLength: 300,
            isRequired: false,
        );

        $this->createBankAccountField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'bankAccountNumber',
            title: 'IBAN',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'bankAccountHolder',
            title: 'Naam rekeninghouder',
            maxLength: 50,
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'certifiedEmploymentDocument',
            title: 'Gewaarmerkt verzekeringsbericht',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 20971520
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'wiaDecisionDocument',
            title: 'WIA-Beslissing',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 20971520
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'isWiaDecisionPostponed',
            title: 'Is WIA beslissing uitgesteld?',
            options: ['Ja', 'Nee']
        );

        //If isWiaDecisionPostponed === yes
        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'wiaDecisionPostponedLetter',
            title: 'Toekenningsbrief',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 20971520,
            requiredCondition: new ComparisonCondition(
                1, 'isWiaDecisionPostponed', Operator::Identical, 'Ja'
            ),
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'employmentContract',
            title: 'Bewijs dienstverband',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 20971520
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'employmentFunction',
            title: 'Functie',
            options: [
                'Ambulancechauffeur',
                'Anesthesiemedewerker en/of operatieassistent',
                'Arts',
                'Bachelor medisch hulpverlener',
                'Doktersassistent',
                'Helpende',
                'Physician assistant',
                'Praktijkondersteuner huisarts',
                'Verpleegkundig specialist',
                '(gespecialiseerd) Verpleegkundige',
                'Verzorgende in de individuele gezondheidszorg (VIG’er) of verzorgende',
                'Zorgondersteuner en/of voedingsassistent',
                'Anders',
            ]
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'otherEmploymentFunction',
            title: 'Andere functie',
            maxLength: 300,
            requiredCondition: new ComparisonCondition(1, 'employmentFunction', Operator::Identical, 'Anders'),
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'employerKind',
            title: 'Werkgever',
            options: ['Zorgaanbieder', 'Andere organisatie']
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'otherEmployerDeclarationFile',
            title: 'Verklaring zorgaanbieder',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 20971520,
            requiredCondition: new OrCondition([
                new ComparisonCondition(1, 'employmentFunction', Operator::Identical, 'Anders'),
                new ComparisonCondition(1, 'employerKind', Operator::Identical, 'Andere organisatie'),
            ]));

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'hasBeenWorkingAtJudicialInstitution',
            title: 'Bent u werkzaamh geweest bij een justitiële inrichting?',
            options: ['Ja', 'Nee']
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'BIGNumberJudicialInstitution',
            title: 'BIG-nummer',
            maxLength: 11,
            requiredCondition: new ComparisonCondition(1, 'hasBeenWorkingAtJudicialInstitution', Operator::Identical, 'Ja'),
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'socialMedicalAssessment',
            title: 'Medisch onderzoeksverslag',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 20971520
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'hasPostCovidDiagnose',
            title: 'Heeft langdurige post-COVID klachten',
            options: ['Ja', 'Nee']
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'doctorsCertificate',
            title: 'Verklaring arts',
            mimeTypes: ['image/*', 'application/pdf'],
            maxFileSize: 20971520,
            requiredCondition: new ComparisonCondition(1, 'hasPostCovidDiagnose', Operator::Identical, 'Nee'),
        );

        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'truthfullyCompleted',
            title: ''
        );
    }

    public function pczmFirstAssessmentFields(): void
    {
        // Eerste beoordeling
        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'checklist',
            title: 'Controlevragen',
            options: [
                "Alle aangeleverde documenten zijn te herleiden tot dezelfde persoon op basis van BSN en de overige persoonsgegevens",
                "Het IBAN bestaat en is actief",
                "Het opgegeven IBAN staat op naam van de aanvrager",
                "Op basis van de SurePay terugkoppeling ben ik akkoord met het opgegeven rekeningnummer"
            ],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'amount',
            title: 'Bedrag',
            options: ['€ 15.000'],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessment',
            title: 'Beoordeling',
            options: ['Onbeoordeeld', 'Aanvulling nodig', 'Afgekeurd', 'Goedgekeurd']
        );
    }

    public function pczmSeconcAssessmentFields(): void
    {
        //Tweede beoordeling
        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            code: 'secondAssessment',
            title: 'Beoordeling',
            options: ['Oneens met de eerste beoordeling', 'Eens met de eerste beoordeling']
        );
    }

    public function pczmInternalAssessmentFields(): void
    {
        //Interne controle
        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessment',
            title: 'Beoordeling',
            options: ['Onbeoordeeld', 'Afgekeurd', 'Goedgekeurd']
        );
    }

    public function pczmCoordinatorImplemenationFields(): void
    {
        //Uitvoeringscoördinator controle
        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            code: 'implementationCoordinatorAssessment',
            title: 'Beoordeling',
            options: ['Afgekeurd', 'Goedgekeurd']
        );
    }
}
