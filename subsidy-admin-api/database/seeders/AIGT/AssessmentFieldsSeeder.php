<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
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
        $this->auditAssessmentFields();
        $this->implementationAssessmentFields();
        $this->assignationDelayPeriodFields();
        $this->assignationAssessmentFields();
        $this->assignationAuditAssessmentFields();
        $this->assignationImplementationAssessmentFields();
    }

    public function firstAssessmentFields(): void
    {
        // Eerste beoordeling
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'firstAssessmentChecklist',
            title:          'Gecontroleerd',
            options:        [
                                'Valt de aanvrager onder de WSNP/bewindvoering?',
                                'Is de aanvraag tijdig ingediend?',
                                'Is het aanvraagformulier volledig ingevuld?',
                                'Is het aanvraagformulier juist ondertekend?',
                                'Bevat de aanvraag alle vereiste documenten?',
                                'Hebben alle ingediende documenten betrekking op de juiste persoon?',
                                'Zijn het inschrijvingsbewijs RGS en het opleidingsbewijs OIGT correct ondertekend?',
                                'Staat de zakenpartner correct in SAP met het juiste bankrekeningnummer?',
                                'Is de einddatum van de buitenlandstage duidelijk?',
                                'Komt dit overeen met de opgave van de OIGT?',
                                'Komt de aanvrager voor in het M&O-register?'
                            ],
            isRequired:     false,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'subsidyAwardedBefore',
            title:          'Reeds eerder subsidie verleend aan dezelfde persoon voor de buitenlandstage?',
            options:        ['Niet eerder subsidie verstrekt', 'Wel eerder subsidie verstrekt'],
            isRequired:     false,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'amount',
            title:          'Bedrag',
            options:        ['€ 17.000'],
            default:        '€ 17.000',
            isRequired:     false,
            requiredCondition: new ComparisonCondition(2, 'firstAssessment', Operator::Identical, 'Goedgekeurd'),
            excludeFromCloneData: true,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'firstAssessment',
            title:          'Beoordeling',
            options:        ['Aanvulling nodig', 'Afgekeurd', 'Goedgekeurd']
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

    public function auditAssessmentFields(): void
    {
        //interne controle
        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code: 'firstAssessorMotivatedValid',
            title: 'De motivatie van de eerste behandelaar is duidelijk en correct',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code: 'internalAssessment',
            title: 'Beoordeling',
            options: ['Eens met de eerste beoordeling', 'Oneens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code: 'internalAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function implementationAssessmentFields(): void
    {
        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            code: 'implementationCoordinatorAssessment',
            title: 'Beoordeling',
            options: ['Eens met de eerste beoordeling', 'Oneens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function assignationDelayPeriodFields(): void
    {
        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_5_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Vaststellen', 'Vorderen', 'Uitstellen'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_5_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_5_UUID,
            code:           'proofOfRegistrationDocument',
            title:          'Bewijs van inschrijving',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );
    }

    public function assignationAssessmentFields(): void
    {
        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_6_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Vaststellen', 'Vorderen', 'Uitstellen'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_6_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_6_UUID,
            code:           'proofOfRegistrationDocument',
            title:          'Bewijs van inschrijving',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );
    }

    public function assignationAuditAssessmentFields(): void
    {
        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_7_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Eens met de beoordeling op de vaststelling', 'Oneens met de beoordeling op de vaststelling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_7_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function assignationImplementationAssessmentFields(): void
    {
        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_8_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Eens met de beoordeling op de vaststelling', 'Oneens met de beoordeling op de vaststelling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_8_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }
}
