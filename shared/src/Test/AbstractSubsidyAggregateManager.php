<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Test;

use League\CommonMark\Exception\LogicException;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @propery Subsidy $subsidy
 */
abstract class AbstractSubsidyAggregateManager
{
    private const REVIEW_PERIOD = 7; // days

    public const VALUE_APPROVED = ApplicationStatus::Approved->value;
    public const VALUE_REJECTED = ApplicationStatus::Rejected->value;
    public const VALUE_REQ_CHANGES = ApplicationStatus::RequestForChanges->value;
    public const VALUE_AGREES = 'agrees';
    public const VALUE_DISAGREES = 'disagrees';
    public const DISAGREE_WITH_FIRST_ASSESSMENT = 'Disagree with first assessment';
    public const AGREE_WITH_FIRST_ASSESSMENT = 'Agree with first assessment';

    private array $users = [];
    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private array $subsidyStages = [];
    private array $subsidyStageFields = [];

    public function __construct(
        private readonly ApplicationStageEncryptionService $encryptionService
    ) {
    }

    public function build(): void
    {
        $this->createSubsidy();
        $this->createSubsidyStages();
        $this->createTransactions();
        $this->createUsers();
    }

    abstract protected function createTransactions(): void;

    abstract protected function createSubsidyStages(): void;

    abstract protected function createUsers(): void;

    protected function createSubsidy(): void
    {
        $this->subsidy = Subsidy::factory()->create()->first();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create([
                         'review_period' => self::REVIEW_PERIOD
                     ])->first();
    }

    public function getSubsidy(): Subsidy
    {
        return $this->subsidy;
    }

    public function getSubsidyVersion(): SubsidyVersion
    {
        return $this->subsidyVersion;
    }

    public function getSubsidyStage(int $stage): SubsidyStage
    {
        return $this->subsidyStages[$stage];
    }

    public function getSubsidyStageField(string $name): Field
    {
        return $this->subsidyStageFields[$name];
    }

    public function createSubsidyStageField(
        string $name,
        SubsidyStage $subsidyStage,
        array $subsidyStageFieldAttributes
    ): Field {
        if (array_key_exists($name, $this->subsidyStageFields)) {
            throw new LogicException(sprintf('SubsidyStageField key already exits. Key: %s', $name));
        }

        $this->subsidyStageFields[$name] =
            Field::factory()->for($subsidyStage)->create(['code' => $name, ...$subsidyStageFieldAttributes])->first();

        return $this->subsidyStageFields[$name];
    }

    public function createSubsidyStage(
        int $stage,
        SubsidyVersion $subsidyVersion,
        array $subsidyStageAttributes
    ): SubsidyStage {
        if (array_key_exists($stage, $this->subsidyStages)) {
            throw new LogicException(sprintf('SubidyStage sequence already exits. Key: %s', $stage));
        }

        $this->subsidyStages[$stage] = SubsidyStage::factory()
            ->for($subsidyVersion)
            ->create(['stage' => $stage, ...$subsidyStageAttributes])
            ->first();

        return $this->subsidyStages[$stage];
    }

    public function createSubsidyStageTransaction(
        int $current,
        int $target,
        array $transitionAttributes
    ): SubsidyStageTransition {
        return SubsidyStageTransition::factory()
            ->for($this->getSubsidyStage($current), 'currentSubsidyStage')
            ->for($this->getSubsidyStage($target), 'targetSubsidyStage')
            ->create($transitionAttributes)
            ->first();
    }

    public function createUser(string $name, RoleEnum $role): User
    {
        $user = User::factory()->create()->first();
        $user->attachRole($role, $this->getSubsidy()->id);

        $this->users[$name] = $user;

        return $user;
    }

    public function getUser(string $name): User
    {
        return $this->users[$name];
    }

    public function getUsers(): array
    {
        return array_values($this->users);
    }

    public function createApplication(): Application
    {
        $identity = Identity::factory()->create();
        return Application::factory()
            ->for($identity)
            ->for($this->getSubsidyVersion())
            ->create()
            ->first();
    }

    public function createApplicationStage(
        Application $application,
        int $stage,
        array $attributes = []
    ): ApplicationStage {
        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        return ApplicationStage::factory()
            ->for($application)
            ->for($this->getSubsidyStage($stage))
            ->create(['encrypted_key' => $encryptedKey, ...$attributes])
            ->first();
    }

    public function createApplicationStageWithAnswer(): void
    {
        //todo
    }

    public function createAnswer(ApplicationStage $applicationStage, Field $field, string $value): Answer
    {
        $encrypter = $this->encryptionService->getEncrypter($applicationStage);
        return Answer::factory()
            ->for($applicationStage)
            ->for($field)
            ->create([
                         'encrypted_answer' => $encrypter->encrypt($value)
                     ])
            ->first();
    }
}
