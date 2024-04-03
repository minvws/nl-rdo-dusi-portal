<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class PCZMAssessmentFieldsSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->firstAssessmentFields();
        $this->secondAssessmentFields();
        $this->internalAssessmentFields();
        $this->coordinatorImplemenationFields();
        $this->increaseAmountFields();
    }

    public function firstAssessmentFields(): void
    {
        // Eerste beoordeling
        //Persoonsgegevens
        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'personalDataChecklist',
            title: 'Controlevragen',
            options: [
                "Alle aangeleverde documenten zijn te herleiden tot dezelfde persoon op basis van BSN en de overige persoonsgegevens",
                "Het IBAN bestaat en is actief",
                "Het opgegeven IBAN staat op naam van de aanvrager of bewindvoerder",
                "Op basis van de SurePay terugkoppeling, en de controle of de aanvrager onder bewind staat, ben ik akkoord met het opgegeven rekeningnummer"
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        //Vaststellen wia
        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'wiaChecklist',
            title: 'Algemeen',
            options: [
                "Het verzekeringsbericht is gewaarmerkt en het BSN is zichtbaar in de upload",
                "Het BSN op het verzekeringsbericht komt overeen met dat van de aanvrager",
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'WIADecisionIndicates',
            title: 'Uit de WIA-beslissing blijkt dat er sprake is van',
            options: [
                'IVA uitkering',
                'WGA uitkering',
                'Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden'
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'IVA_Or_WIA_Checklist',
            title: 'IVA- of WGA-uitkering',
            options: [
                'Niet van toepassing',
                'Op het verzekeringsbericht staat vermeld dat de aanvrager een WIA-uitkering ontvangt',
                'De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht',
                'De eerste ziektedag ligt in de periode van de eerste golf (1 maart 2020 tot 1 juli 2020)'
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'WIA_RejectedOnHighSalaryChecklist',
            title: 'Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden',
            options: [
                "Niet van toepassing",
                "De datum waarop de WIA-uitkering niet wordt ontvangen ligt in de periode van 1 maart 2022 tot 1 september 2022 (104 weken wachttijd)"
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        //Zorgaanbieder en functie
        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'employerChecklist',
            title: 'Controlevragen',
            options: [
                "De werkgever komt overeen met de laatste werkgever vóór de WIA in het verzekeringsbericht",
                "Uit de arbeidsovereenkomst en/of de verklaring van de zorgaanbieder blijkt dat er sprake is van werkzaamheden die binnen de subsidieregeling vallen"
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderStatementIsComplete',
            title: 'De verklaring van de zorgaanbieder is volledig ingevuld',
            options: [
                'Nee',
                'Ja',
                'Niet van toepassing',
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'employerName',
            title: 'Naam werkgever',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderName',
            title: 'Naam zorgaanbieder, indien niet werkgever',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'chamberOfCommerceNumberHealthcareProvider',
            title: 'KVK-nummer van de zorgaanbieder waar de zorg is verleend',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderChecklist',
            title: 'Controlevragen',
            options: [
                "De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste SBI-code",
                "De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste AGB code of is een Jeugdhulp aanbieder die op de lijst staat",
                "De zorgaanbieder voldoet aan de eisen binnen de regeling"
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderSBICode',
            title: 'SBI-code zorgaanbieder',
            params:         ['maxLength' => 100],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'healthcareProviderAGBCode',
            title: 'AGB-code zorgaanbieder',
            params:         ['maxLength' => 100],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        //Justitiële inrichting
        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'judicialInstitutionIsEligible',
            title: 'De justitiële inrichting waar de aanvrager werkzaam is geweest valt binnen de regeling',
            options: ['Nee', 'Ja', 'Niet van toepassing'],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'applicantFoundInBigRegister',
            title: 'De aanvrager is op basis van het doorgegeven BIG-nummer terug te vinden in het BIG-register',
            options: ['Nee', 'Ja', 'Niet van toepassing'],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        //Vaststellen post-COVID
        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'postCovidChecklist',
            title: 'Controlevragen',
            options: [
                "Op basis van het medisch onderzoeksverslag (medische rapportage) en/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld",
                "De post-COVID diagnose is vóór 1 juni 2023 gesteld",
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'doctorFoundInBigRegister',
            title: 'De arts die de verklaring heeft afgegeven is als arts geregistreerd in het BIG-register',
            options: [
                'Nee',
                'Ja',
                'Niet van toepassing'
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'doctorsCertificateIsComplete',
            title: 'De verklaring van de arts is volledig ingevuld',
            options: [
                'Nee',
                'Ja',
                'Niet van toepassing',
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        //Status
        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'amount',
            title: 'Bedrag',
            options: ['€ 15.000'],
            default: '€ 15.000',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessment',
            title: 'Beoordeling',
            options: ['Aanvulling nodig', 'Afgekeurd', 'Goedgekeurd'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        //Toelichting
        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementReason',
            title: 'Reden',
            options: ['Incomplete aanvraag', 'Onduidelijkheid of vervolgvragen'],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementNote',
            title: 'Toelichting van benodigde aanvullingen',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentRejectedNote',
            title: 'Reden van afkeuring',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentApprovedNote',
            title: 'Motivatie van goedkeuring',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_2_UUID,
            code: 'firstAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function secondAssessmentFields(): void
    {
        //Tweede beoordeling
        $this->createCheckboxField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
            code: 'firstAssessorMotivatedValid',
            title: 'De motivatie van de eerste behandelaar is duidelijk en correct',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
            code: 'secondAssessment',
            title: 'Beoordeling',
            options: ['Oneens met de eerste beoordeling', 'Eens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_3_UUID,
            code: 'secondAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function internalAssessmentFields(): void
    {
        //Interne controle
        $this->createMultiSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessmentChecklist',
            title: 'Controlevragen',
            options: [
                "Alle benodigde documenten zijn aangeleverd",
                "Uit de dataverificatie blijkt dat er geen onvolkomenheden zijn geconstateerd",
                "De motivatie van de eerste beoordeling is duidelijk"
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessment',
            title: 'Beoordeling',
            options: ['Afgekeurd', 'Goedgekeurd'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_4_UUID,
            code: 'internalAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function coordinatorImplemenationFields(): void
    {
        //Uitvoeringscoördinator controle
        $this->createSelectField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
            code: 'implementationCoordinatorAssessment',
            title: 'Beoordeling',
            options: ['Afgekeurd', 'Goedgekeurd'],
            retentionPeriod: DataRetentionPeriod::Long
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
            code: 'coordinatorImplementationInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
            code: 'coordinatorImplementationReasonForRejection',
            title: 'Reden van afkeuring',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_5_UUID,
            code: 'coordinatorImplementationApprovalNote',
            title: 'Extra informatie over de gedane wijzigingen',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function increaseAmountFields(): void
    {
        $this->createTextField(
            subsidyStageId: PCZMSubsidyStagesSeeder::PCZM_STAGE_6_UUID,
            code: 'increaseAmountInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }
}
