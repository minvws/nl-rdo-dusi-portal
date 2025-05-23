<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Test;

use League\CommonMark\Exception\LogicException;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @propery Subsidy $subsidy
 *
 * @psalm-suppress InvalidReturnStatement
 * @psalm-suppress InvalidReturnType
 *
 * @SuppressWarnings("CouplingBetweenObjects")
 */
abstract class AbstractSubsidyAggregateManager
{
    protected const REVIEW_PERIOD = 7; // days

    protected array $users = [];
    protected Subsidy $subsidy;
    protected SubsidyVersion $subsidyVersion;
    protected array $subsidyStages = [];
    protected array $subsidyStageFields = [];

    public function __construct(
        private readonly ApplicationStageEncryptionService $encryptionService
    ) {
    }

    public function setup(): void
    {
        $this->createSubsidy();
        $this->createSubsidyStages();
        $this->createTransitions();
        $this->createUsers();
    }

    abstract protected function createTransitions(): void;

    abstract protected function createSubsidyStages(): void;

    abstract protected function createUsers(): void;

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function createSubsidy(): void
    {
        $this->subsidy = Subsidy::factory()->create();
        $this->subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create([
                         'review_period' => self::REVIEW_PERIOD
                     ]);
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

        $this->subsidyStageFields[$name] = Field::factory()->for($subsidyStage)->create([
            'code' => $name,
            'is_required' => false,
            ...$subsidyStageFieldAttributes
        ]);

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
            ->create(['stage' => $stage, ...$subsidyStageAttributes]);

        return $this->subsidyStages[$stage];
    }

    public function createSubsidyStageTransition(
        int $current,
        ?int $target,
        array $transitionAttributes
    ): SubsidyStageTransition {
        $subsidyStageTransition =  SubsidyStageTransition::factory()
            ->for($this->getSubsidyStage($current), 'currentSubsidyStage')
            ->create($transitionAttributes);

        if ($target) {
            $subsidyStageTransition->targetSubsidyStage()->associate($this->getSubsidyStage($target));
            $subsidyStageTransition->save();
        }

        return $subsidyStageTransition;
    }

    public function createUser(string $name, RoleEnum $role): User
    {
        $user = User::factory()->create();
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
            ;
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
            ;
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
            ;
    }
}
