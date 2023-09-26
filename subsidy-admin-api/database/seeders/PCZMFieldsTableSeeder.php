<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class PCZMFieldsTableSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPCZMFields();
    }

    public function createPCZMFields(): void
    {
        $this->pczmApplicationFields();
        $this->pczmFirstAssessmentFields();
        $this->pczmSecondAssessmentFields();
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
        //Persoonsgegevens
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'personalDataChecklist',
            title: 'Controlevragen',
            options: [
                "Alle aangeleverde documenten zijn te herleiden tot dezelfde persoon op basis van BSN en de overige persoonsgegevens",
                "Het IBAN bestaat en is actief",
                "Het opgegeven IBAN staat op naam van de aanvrager",
                "Op basis van de SurePay terugkoppeling ben ik akkoord met het opgegeven rekeningnummer"
            ],
            isRequired: false
        );

        //Vaststellen wia
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'wiaChecklist',
            title: 'Controlevragen',
            options: [
                "Het verzekeringsbericht is gewaarmerkt en het BSN is zichtbaar in de upload",
                "Het BSN op het verzekeringsbericht komt overeen met dat van de aanvrager",
                "Uit het verzekeringsbericht blijkt dat de aanvrager in de WIA zit",
                "De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht",
                "Uit de WIA-beslissing blijkt dat de aanvrager in aanmerking komt voor subsidie",
            ],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'firstSickDayWithinExpiryDate',
            title: 'Uit de toekenningsbrief van de afgesproken loondoorbetaling blijkt dat de eerste ziektedag in de periode van 1 maart 2020 en 1 juli 2020 ligt',
            options: [
                'Nee',
                'Ja',
                'Niet van toepassing'
            ],
            isRequired: false
        );

        //Zorgaanbieder en functie
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'employerChecklist',
            title: 'Controlevragen',
            options: [
                "De werkgever komt overeen met de laatste werkgever vóór de WIA in het verzekeringsbericht",
                "Uit de arbeidsovereenkomst en/of de verklaring van de zorgaanbieder blijkt dat er sprake is van werkzaamheden die binnen de subsidieregeling vallen"
            ],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderStatementIsComplete',
            title: 'De verklaring van de zorgaanbieder is volledig ingevuld',
            options: [
                'Nee',
                'Ja',
                'Niet van toepassing',
            ],
            isRequired: false
         );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'employerName',
            title: 'Naam werkgever',
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderName',
            title: 'Naam zorgaanbieder, indien niet werkgever',
            isRequired: false
        );


        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'chamberOfCommerceNumberEmployer',
            title: 'KVK-nummer werkgever',
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'chamberOfCommerceNumberHealtcareProvider',
            title: 'KVK-nummer zorgaanbieder, indien niet werkgever',
            isRequired: false
        );

        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderChecklist',
            title: 'Controlevragen',
            options: [
                "De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste SBI-code",
                "De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste AGB-code",
                "De zorgaanbieder voldoet aan de eisen binnen de regeling"
            ],
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderSBICode',
            title: 'SBI-code zorgaanbieder',
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderAGBCode',
            title: 'AGB-code zorgaanbieder',
            isRequired: false
        );

        //Justitiële inrichting
        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'judicialInstitutionIsEligible',
            title: 'De justitiële inrichting waar de aanvrager werkzaam is geweest valt binnen de regeling',
            options: ['Nee', 'Ja', 'Niet van toepassing'],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'applicantFoundInBigRegister',
            title: 'De aanvrager is op basis van het doorgegeven BIG-nummer terug te vinden in het BIG-register',
            options: ['Nee', 'Ja', 'Niet van toepassing'],
            isRequired: false
        );

        //Vaststellen post-COVID
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'postCovidChecklist',
            title: 'Controlevragen',
            options: [
                "Op basis van het sociaal-medisch verslag en/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld",
                "De post-COVID diagnose is vóór 1 juni 2023 gesteld",
            ],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'doctorFoundInBigRegister',
            title: 'De arts die de verklaring heeft afgegeven is als arts geregistreerd in het BIG-register',
            options: [
                'Nee',
                'Ja',
                'Niet van toepassing'
            ],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'doctorsCertificateIsComplete',
            title: 'De verklaring van de arts is volledig ingevuld',
            options: [
                'Nee',
                'Ja',
                'Niet van toepassing',
            ],
            isRequired: false
        );

        //Status
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

    public function pczmSecondAssessmentFields(): void
    {
        //Tweede beoordeling
        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            code: 'firstAssessorMotivatedValid',
            title: 'De motivatie van de eerste behandelaar is duidelijk en correct',
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            code: 'secondAssessment',
            title: 'Beoordeling',
            options: ['Oneens met de eerste beoordeling', 'Eens met de eerste beoordeling']
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_3_UUID,
            code: 'secondAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false
        );
    }

    public function pczmInternalAssessmentFields(): void
    {
        //Interne controle
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessmentChecklist',
            title: 'Controlevragen',
            options: [
                "Alle benodigde documenten zijn aangeleverd",
                "Uit de dataverificatie blijkt dat er geen onvolkomenheden zijn geconstateerd",
                "De motivatie van de eerste beoordeling is duidelijk"
            ],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessment',
            title: 'Beoordeling',
            options: ['Onbeoordeeld', 'Afgekeurd', 'Goedgekeurd']
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessmentNote',
            title: 'Extra informatie over de beoordeling',
            isRequired: false
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

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            code: 'coordinatorImplementationNote',
            title: 'Interne notitie',
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            code: 'coordinatorImplementationReasonForRejection',
            title: 'Extra informatie over de gedane wijzigingen',
            isRequired: false,
            requiredCondition: new ComparisonCondition(5, 'implementationCoordinatorAssessment', Operator::Identical, 'Afgekeurd'),
        );
    }
}
