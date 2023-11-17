<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Repositories;

use Carbon\Carbon;
use Faker\Core\Uuid;
use Illuminate\Database\QueryException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\User\Models\Role as UserRole;
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

    public function testFind()
    {
        $user = User::factory()->create();
        $this->assertEquals($user->id, $this->repository->find($user->id)->id);
    }

    public function testGetPotentialUsersWithSpecificRole()
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
        $implementationCoordinator = User::factory()->create([
            'name' => 'implementationCoordinator'
        ]);
        $implementationCoordinator->roles()->attach(Role::ImplementationCoordinator, [
            'subsidy_id' => $this->subsidy->id,
            'role_name' => Role::ImplementationCoordinator,
        ]);
        $actual_result = $this->repository->getPotentialUsersWithSpecificRole(
            $this->subsidyStage,
            [$assessor1->id],
            'found'
        );
        $this->assertEquals(2, $actual_result->count());
        $this->assertEquals(
            $assessor4->name,
            $actual_result->first()->name
        );
        $this->assertEquals(
            $assessor4->id,
            $actual_result->first()->id
        );
    }
}
