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
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
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
    private const SUBSIDY_PZCM_ID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function createSubsidy(): void
    {
        $this->subsidy = Subsidy::factory()
            ->create([
                'id' => self::SUBSIDY_PZCM_ID,
            ]);
        $this->subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create([
                'review_period' => self::REVIEW_PERIOD
            ]);
    }

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
                    AssessmentOutcome::UNASSESSED,
                    AssessmentOutcome::SUPPLEMENT_NEEDED,
                    AssessmentOutcome::APPROVED,
                    AssessmentOutcome::REJECTED,
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
                    AssessmentOutcome::AGREES,
                    AssessmentOutcome::DISAGREES,
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
                    AssessmentOutcome::APPROVED,
                    AssessmentOutcome::REJECTED,
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

    /**
     * @SuppressWarnings("ExcessiveMethodLength")
     */
    public function createTransitions(): void
    {
        $this->createSubsidyStageTransition(1, 2, [
            'description' => 'Aanvraag ingediend',
            'target_application_status' => ApplicationStatus::Pending,
            'assign_to_previous_assessor' => true,
            'clone_data' => true,
            'condition' => null,
            'send_message' => false,
        ]);

        $this->createSubsidyStageTransition(2, 1, [
            'description' => 'Aanvulling gevraagd',
            'target_application_status' => ApplicationStatus::RequestForChanges,
            'assign_to_previous_assessor' => true,
            'clone_data' => true,
            'condition' => new ComparisonCondition(
                2,
                'firstAssessment',
                Operator::Identical,
                AssessmentOutcome::SUPPLEMENT_NEEDED
            ),
            'send_message' => true,
        ]);

        $this->createSubsidyStageTransition(2, 3, [
            'description' => 'Eerste beoordeling voltooid',
            'condition' => new InCondition(
                2,
                'firstAssessment',
                [AssessmentOutcome::APPROVED, AssessmentOutcome::REJECTED]
            ),
            'send_message' => false,
        ]);

        $this->createSubsidyStageTransition(3, 2, [
            'target_application_status' => ApplicationStatus::RequestForChanges,
            'condition' => new ComparisonCondition(
                3,
                'secondAssessment',
                Operator::Identical,
                AssessmentOutcome::DISAGREES
            ),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true,
        ]);

        $this->createSubsidyStageTransition(3, null, [
            'description' => 'Tweede beoordeling eens met afkeuring eerste beoordeling',
            'target_application_status' => ApplicationStatus::Rejected,
            'condition' => new AndCondition([
                new ComparisonCondition(
                    2,
                    'firstAssessment',
                    Operator::Identical,
                    AssessmentOutcome::REJECTED
                ),
                new ComparisonCondition(
                    3,
                    'secondAssessment',
                    Operator::Identical,
                    AssessmentOutcome::AGREES
                )
            ]),
            'send_message' => true
        ]);

        $this->createSubsidyStageTransition(3, 4, [
            'description' => 'Tweede beoordeling eens met goedkeuring eerste beoordeling',
            'condition' => new AndCondition([
                new ComparisonCondition(
                    2,
                    'firstAssessment',
                    Operator::Identical,
                    AssessmentOutcome::APPROVED
                ),
                new ComparisonCondition(
                    3,
                    'secondAssessment',
                    Operator::Identical,
                    AssessmentOutcome::AGREES
                ),
            ]),
            'send_message' => false,
        ]);

        $this->createSubsidyStageTransition(4, 2, [
            'description' => 'Interne controle oneens met beoordeling',
            'condition' => new OrCondition([
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        AssessmentOutcome::REJECTED
                    ),
                    new ComparisonCondition(
                        4,
                        'internalAssessment',
                        Operator::Identical,
                        AssessmentOutcome::APPROVED
                    ),
                   ]),
                new AndCondition([
                    new ComparisonCondition(
                        2,
                        'firstAssessment',
                        Operator::Identical,
                        AssessmentOutcome::APPROVED
                    ),
                    new ComparisonCondition(
                        4,
                        'internalAssessment',
                        Operator::Identical,
                        AssessmentOutcome::REJECTED
                    ),
                ]),
            ]),
            'send_message' => false,
            'assign_to_previous_assessor' => true,
            'clone_data' => true
        ]);

        $this->createSubsidyStageTransition(4, 5, [
           'description' => 'Interne controle eens met beoordeling',
           'condition' => new AndCondition([
                new ComparisonCondition(
                    2,
                    'firstAssessment',
                    Operator::Identical,
                    AssessmentOutcome::APPROVED
                ),
                new ComparisonCondition(
                    4,
                    'internalAssessment',
                    Operator::Identical,
                    AssessmentOutcome::APPROVED
                )
           ]),
           'send_message' => false,
        ]);

        $this->createSubsidyStageTransition(5, null, [
                   'description' => 'Aanvraag afgekeurd',
                   'target_application_status' => ApplicationStatus::Rejected,
                   'condition' => new ComparisonCondition(
                       5,
                       'implementationCoordinatorAssessment',
                       Operator::Identical,
                       AssessmentOutcome::REJECTED
                   ),
                   'send_message' => true
               ]);


        $this->createSubsidyStageTransition(5, null, [
           'description' => 'Aanvraag goedgekeurd',
           'target_application_status' => ApplicationStatus::Approved,
           'condition' =>
               new ComparisonCondition(
                   5,
                   'implementationCoordinatorAssessment',
                   Operator::Identical,
                   AssessmentOutcome::APPROVED
               ),
           'send_message' => true
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
