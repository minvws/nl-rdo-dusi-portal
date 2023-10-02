<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class PCZMAssessmentFieldsTableSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->pczmFirstAssessmentFields();
        $this->pczmSecondAssessmentFields();
        $this->pczmInternalAssessmentFields();
        $this->pczmCoordinatorImplemenationFields();
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
                "Het opgegeven IBAN staat op naam van de aanvrager of bewindvoerder",
                "Op basis van de SurePay terugkoppeling, en de controle of de aanvrager onder bewind staat, ben ik akkoord met het opgegeven rekeningnummer"
            ],
            isRequired: false
        );

        //Vaststellen wia
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'wiaChecklist',
            title: 'Algemeen',
            options: [
                "Het verzekeringsbericht is gewaarmerkt en het BSN is zichtbaar in de upload",
                "Het BSN op het verzekeringsbericht komt overeen met dat van de aanvrager",
            ],
            isRequired: false
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'WIADecisionIndicates',
            title: 'Uit de WIA-beslissing blijkt dat er sprake is van',
            options: [
                'IVA uitkering',
                'WGA uitkering',
                'Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden'
            ],
            isRequired: false
        );

        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'IVA_Or_WIA_Checklist',
            title: 'IVA- of WGA-uitkering',
            options: [
                'Niet van toepassing',
                'Op het verzekeringsbericht staat vermeld dat de aanvrager een WIA-uitkering ontvangt',
                'De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht',
                'De eerste ziektedag ligt in de periode van de eerste golf (1 maart 2020 tot 1 juli 2020)'
            ],
            isRequired: false
        );

        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'WIA_RejectedOnHighSalaryChecklist',
            title: 'Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden',
            options: [
                "Niet van toepassing",
                "De datum waarop de WIA-uitkering niet wordt ontvangen ligt in de periode van 1 maart 2022 tot 1 september 2022 (104 weken wachttijd)"
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
            code: 'chamberOfCommerceNumberHealthcareProvider',
            title: 'KVK-nummer van de zorgaanbieder waar de zorg is verleend',
            isRequired: false
        );

        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderChecklist',
            title: 'Controlevragen',
            options: [
                "De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste SBI-code",
                "De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste AGB code of is een Jeugdhulp aanbieder die op de lijst staat",
                "De zorgaanbieder voldoet aan de eisen binnen de regeling"
            ],
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderSBICode',
            title: 'SBI-code zorgaanbieder',
            maxLength: 100,
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderAGBCode',
            title: 'AGB-code zorgaanbieder',
            maxLength: 100,
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
                "Op basis van het medisch onderzoeksverslag (medische rapportage) en/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld",
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
            options: ['Aanvulling nodig', 'Afgekeurd', 'Goedgekeurd']
        );

        //Toelichting
        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementReason',
            title: 'Reden',
            options: ['Incomplete aanvraag', 'Onduidelijkheid of vervolgvragen'],
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementNote',
            title: 'Toelichting van benodigde aanvullingen',
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentRejectedNote',
            title: 'Reden van afkeuring',
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false
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
            options: ['Afgekeurd', 'Goedgekeurd']
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessmentInternalNote',
            title: 'Interne notitie',
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
            code: 'coordinatorImplementationInternalNote',
            title: 'Interne notitie',
            isRequired: false
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            code: 'coordinatorImplementationReasonForRejection',
            title: 'Reden van afkeuring',
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_5_UUID,
            code: 'coordinatorImplementationApprovalNote',
            title: 'Extra informatie over de gedane wijzigingen',
            isRequired: false,
        );
    }
}
