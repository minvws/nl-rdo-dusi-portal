<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Test;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\AndCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\InCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;

/**
 * Setup of a subsidy which the following requirements
 * - stage 1: Application
 * - stage 2: First assessment
 * - stage 3: Second assessment
 * - stage 4: Internal audit
 * - stage 5: Implementation coordinator assessment
 *
 * This setup is equal to the PCZM (Post-Covid Zorg Medewerkers) subsidy.
 */
class ComplexSubsidyAggregateManager extends AbstractSubsidyAggregateManager
{
    protected function createSubsidyStages(): void
    {
        $subsidyStage1 =
            $this->createSubsidyStage(1, $this->getSubsidyVersion(), [
                'subject_role' => SubjectRole::Applicant,
            ]);

        $this->createSubsidyStageField('lastName', $subsidyStage1, ['type' => FieldType::Text]);
        $this->createSubsidyStageField(
            'employmentContract',
            $subsidyStage1,
            ['type' => FieldType::Upload]
        );

        $subsidyStage2 =
            $this->createSubsidyStage(2, $this->getSubsidyVersion(), [
                'subject_role' => SubjectRole::Assessor,
                'assessor_user_role' => Role::Assessor,
            ]);
        $this->createSubsidyStageField('firstAssessment', $subsidyStage2, [
            'type' => FieldType::Select,
            'params' => [
                'options' => [
                    "Onbeoordeeld",
                    "Aanvulling nodig",
                    self::VALUE_APPROVED,
                    self::VALUE_REJECTED,
                ],
            ],
        ]);

        $subsidyStage3 =
            $this->createSubsidyStage(3, $this->getSubsidyVersion(), [
                'subject_role' => SubjectRole::Assessor,
                'assessor_user_role' => Role::Assessor,
            ]);
        $this->createSubsidyStageField('secondAssessment', $subsidyStage3, [
            'type' => FieldType::Select,
            'params' => [
                'options' => [
                    self::AGREE_WITH_FIRST_ASSESSMENT,
                    self::DISAGREE_WITH_FIRST_ASSESSMENT,
                ],
            ],
        ]);

        $subsidyStage4 =
            $this->createSubsidyStage(4, $this->getSubsidyVersion(), [
                'subject_role' => SubjectRole::Assessor,
                'assessor_user_role' => Role::InternalAuditor,
            ]);
        $this->createSubsidyStageField('internalAssessment', $subsidyStage4, [
            'type' => FieldType::Select,
            'params' => [
                'options' => [
                    self::VALUE_APPROVED,
                    self::VALUE_REJECTED,
                ],
            ],
        ]);

        $subsidyStage5 =
            $this->createSubsidyStage(5, $this->getSubsidyVersion(), [
                'subject_role' => SubjectRole::Assessor,
                'assessor_user_role' => Role::ImplementationCoordinator,
            ]);
        $this->createSubsidyStageField(
            'implementationCoordinatorAssessment',
            $subsidyStage5,
            ['type' => FieldType::Select]
        );
    }

    public function createTransactions(): void
    {
        $this->createSubsidyStageTransaction(1, 2, [
            'target_application_status' => ApplicationStatus::Submitted,
            'assign_to_previous_assessor' => true,
            'clone_data' => true,
            'condition' => null,
        ]);

        $this->createSubsidyStageTransaction(2, 3, [
            'condition' => new InCondition(
                2,
                'firstAssessment',
                [self::VALUE_APPROVED, self::VALUE_REJECTED]
            ),
        ]);


        $this->createSubsidyStageTransaction(3, 2, [
            'target_application_status' => ApplicationStatus::RequestForChanges,
            'clone_data' => true,
            'send_message' => true,
            'condition' => new ComparisonCondition(
                3,
                'secondAssessment',
                Operator::Identical,
                self::DISAGREE_WITH_FIRST_ASSESSMENT
            ),
            'assign_to_previous_assessor' => true,
        ]);

        $this->createSubsidyStageTransaction(3, 4, [
            'condition' => new AndCondition([
                new ComparisonCondition(
                    2,
                    'firstAssessment',
                    Operator::Identical,
                    self::VALUE_APPROVED
                ),
                new ComparisonCondition(
                    3,
                    'secondAssessment',
                    Operator::Identical,
                    self::AGREE_WITH_FIRST_ASSESSMENT
                ),
            ]),
        ]);

        $this->createSubsidyStageTransaction(4, 2, [
            'description' => 'Interne controle oneens met beoordeling',
            'condition' => new OrCondition([
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        self::VALUE_REJECTED
                    ),
                    new ComparisonCondition(
                        4,
                        'internalAssessment',
                        Operator::Identical,
                        self::VALUE_APPROVED
                    ),
                   ]),
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        self::VALUE_APPROVED
                    ),
                    new ComparisonCondition(
                        4,
                        'internalAssessment',
                        Operator::Identical,
                        self::VALUE_REJECTED
                    ),
                ]),
            ]),
            'assign_to_previous_assessor' => true,
            'clone_data' => true,
        ]);
    }

    public function createUsers(): void
    {
        $this->createUser('assessor1', RoleEnum::Assessor);
        $this->createUser('assessor2', RoleEnum::Assessor);
        $this->createUser('internalAuditor', RoleEnum::InternalAuditor);
        $this->createUser('implementationCoordinator', RoleEnum::ImplementationCoordinator);
    }
}
