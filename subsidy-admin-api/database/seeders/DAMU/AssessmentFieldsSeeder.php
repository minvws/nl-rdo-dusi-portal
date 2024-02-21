<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU;

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
        $this->internalAssessmentFields();
        $this->coordinatorImplementationFields();
    }

    public function firstAssessmentFields(): void
    {
        // Eerste beoordeling
        $this->createMultiSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'firstAssessmentChecklist',
            title:          'Gecontroleerd',
            options:        [
                                'ToDo?',
                                'To be done?',
                            ],
            isRequired:     false,
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

    public function coordinatorImplementationFields(): void
    {
        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code:           'amount',
            title:          'Bedrag',
            isRequired:     false,
            requiredCondition: new ComparisonCondition(2, 'firstAssessment', Operator::Identical, 'Goedgekeurd'),
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            code: 'implementationCoordinatorAssessment',
            title: 'Beoordeling',
            options: ['Eens met de eerste beoordeling', 'Oneens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
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
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            code:           'internalAssessmentChecklist',
            title:          'Gecontroleerd',
            options:        [
                'ToDo?',
                'To be done?',
            ],
            isRequired:     false,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
            code: 'internalAssessment',
            title: 'Is de verplichting goedgekeurd?',
            options: ['Ja', 'Nee', 'Nvt'],
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
