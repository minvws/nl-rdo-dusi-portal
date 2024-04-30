<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class AssessmentFieldsSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->firstAssessmentFields();
        $this->internalAssessmentFields();
        $this->coordinatorImplementationFields();
    }

    public function firstAssessmentFields(): void
    {
        // Eerste beoordeling
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'firstAssessmentChecklist',
            title: 'Gecontroleerd',
            options: [
                'Woont de aanvrager niet in Caribisch Nederland?',
                'Is het inschrijvingsbewijs van de DAMU-school aangeleverd?',
                'Is naam van de leerling op het DAMU inschrijvingsbewijs hetzelfde als waarvoor subsidie wordt aangevraagd?',
                'Is het inschrijvingsbewijs van de HBO school aangeleverd?',
                'Is naam van de leerling op het HBO inschrijvingsbewijs hetzelfde als waarvoor subsidie wordt aangevraagd?',
                'Is een recente inkomensverklaring (van beide ouders) aangeleverd (maximaal 2 kalenderjaren oud)?',
                'Zijn onnodige gegevens onleesbaar gemaakt?'
            ],
            isRequired: false,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'isMinimumTravelDistanceMet',
            title: 'Is voldaan aan de minimale reisafstand tussen het woonadres en de DAMU-school, volgens de ANWB routeplanner?',
            options: ['Ja', 'Nee'],
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'actualTravelDistanceSingleTrip',
            title: 'Reisafstand volgens de ANWB routeplanner',
            inputMode: 'float',
            params: ['minimum' => 1, 'maximum' => 9999],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'isSubmittedYearlyIncomeCorrect',
            title: 'Is het ingevulde gezamenlijk jaarinkomen correct?',
            options: ['Ja', 'Nee'],
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'actualAnnualJointIncome',
            title: 'Werkelijk gezamenlijk jaarinkomen',
            inputMode: 'numeric',
            params: ['minimum' => 0],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'actualTravelExpenseReimbursement',
            title:          'Werkelijke klilometervergoeding',
            inputMode:      'float',
            params:         ['readonly' => true],
            isRequired:     false,
            source:         FieldSource::Calculated,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'actualRequestedSubsidyAmount',
            title:          'Werkelijjk aangevraagd subsidie bedrag',
            inputMode:      'float',
            params:         ['readonly' => true],
            isRequired:     false,
            source:         FieldSource::Calculated,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'businessPartnerNumber',
            title:          'Zakenpartnernummer',
            inputMode:      'numeric',
            isRequired:     false,
            params:         ['minimum' => 0],
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'decisionCategory',
            title: 'Soort beoordeling',
            options: ['Toewijzing', 'Afwijzing', 'Bijstelling', 'Hardheidsclausule'],
            isRequired:     false,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'firstAssessment',
            title: 'Beoordeling',
            options: ['Aanvulling nodig', 'Afgekeurd', 'Goedgekeurd']
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementReason',
            title: 'Reden',
            options: ['Incomplete aanvraag', 'Onduidelijkheid of vervolgvragen'],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementNote',
            title: 'Toelichting van benodigde aanvullingen',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'firstAssessmentRejectedNote',
            title: 'Reden van afkeuring',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'firstAssessmentApprovedNote',
            title: 'Motivatie van goedkeuring',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code: 'firstAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function coordinatorImplementationFields(): void
    {
        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code: 'implementationCoordinatorAssessment',
            title: 'Beoordeling',
            options: ['Eens met de eerste beoordeling', 'Oneens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code:           'amount',
            title:          'Toegekend bedrag',
            isRequired:     true,
            requiredCondition: new AndCondition([
                new ComparisonCondition(2, 'firstAssessment', Operator::Identical, 'Goedgekeurd'),
                new ComparisonCondition(3, 'implementationCoordinatorAssessment', Operator::Identical, 'Eens met de eerste beoordeling'),
            ])
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code: 'implementationCoordinatorAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function internalAssessmentFields(): void
    {
        //interne controle
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            code: 'internalAssessmentChecklist',
            title: 'Gecontroleerd',
            options: [
                'Valt de aanvrager onder de WSNP/bewindvoering?',
                'Is het subsidiebedrag juist vermeld in SAP?',
                'Komt het IBAN op de aanvraag overeen met SAP?',
                'Is de aangemaakte verplichting geboekt op juiste budgetplaats en budgetpositie?'
            ],
            isRequired: false,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            code: 'subsidyObligationApproved',
            title: 'Is de verplichting goedgekeurd?',
            options: ['Ja', 'Nee', 'Nvt'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            code: 'internalAssessment',
            title: 'Beoordeling',
            options: ['Eens met de eerste beoordeling', 'Oneens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
        );


        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            code: 'internalAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }
}
