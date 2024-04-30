<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Repositories;

use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\User\Repositories\UserRepository;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create([
            'email' => 'assessor@example.com'
        ]);

        $this->subsidy = Subsidy::factory()->create([
            'title' => 'some_subsidy_title',
            'code' => 'SST',
        ]);
        $this->subsidyVersion = SubsidyVersion::factory()->for($this->subsidy)->create();
        $this->subsidyStage = SubsidyStage::factory()->for($this->subsidyVersion)->create(['stage' => 1]);

        $this->repository = new UserRepository();
    }

    public function testFind(): void
    {
        $user = User::factory()->create();
        $this->assertEquals($user->id, $this->repository->find($user->id)->id);
    }

    public function testGetPotentialUsersWithSpecificRole(): void
    {
        $assessor1 = User::factory()->create([
            'name' => 'assessor 1'
        ]);
        $assessor1->roles()->attach(Role::Assessor, [
            'subsidy_id' => $this->subsidy->id,
            'role_name' => Role::Assessor,
        ]);
        $assessor2 = User::factory()->create([
            'name' => 'Found assessor 2'
        ]);
        $assessor2->roles()->attach(Role::Assessor, [
            'subsidy_id' => $this->subsidy->id,
            'role_name' => Role::Assessor,
        ]);
        $assessor3 = User::factory()->create([
            'name' => 'assessor 3'
        ]);
        $assessor3->roles()->attach(Role::Assessor, [
            'subsidy_id' => fake()->uuid(),
            'role_name' => Role::Assessor,
        ]);
        $assessor4 = User::factory()->create([
            'name' => 'assessor found 4'
        ]);
        $assessor4->roles()->attach(Role::Assessor, [
            'subsidy_id' => $this->subsidy->id,
            'role_name' => Role::Assessor,
        ]);
        $assessor5 = User::factory()->create([
            'name' => 'assessor 5'
        ]);
        $assessor5->roles()->attach(Role::Assessor, [
            'subsidy_id' => $this->subsidy->id,
            'role_name' => Role::Assessor,
        ]);
        $assessor6 = User::factory()->create([
            'name' => 'assessor not found 6',
            'active_until' => now()->subDay(),
        ]);
        $assessor6->roles()->attach(Role::Assessor, [
            'subsidy_id' => $this->subsidy->id,
            'role_name' => Role::Assessor,
        ]);
        $implementationCoordinator = User::factory()->create([
            'name' => 'implementationCoordinator not found'
        ]);
        $implementationCoordinator->roles()->attach(Role::ImplementationCoordinator, [
            'subsidy_id' => $this->subsidy->id,
            'role_name' => Role::ImplementationCoordinator,
        ]);
        $actualResult = $this->repository->getPotentialUsersWithSpecificRole(
            $this->subsidyStage,
            [$assessor1->id],
            'found'
        );
        $this->assertEquals(2, $actualResult->count());
        $this->assertEquals(
            $assessor4->name,
            $actualResult->first()->name
        );
        $this->assertEquals(
            $assessor4->id,
            $actualResult->first()->id
        );
    }
}
