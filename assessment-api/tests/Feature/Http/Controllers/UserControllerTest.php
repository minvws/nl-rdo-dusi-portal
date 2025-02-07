<?php

declare(strict_types=1);

namespace Feature\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @group user
 */
class UserControllerTest extends TestCase
{
    private Subsidy $subsidy1;
    private Subsidy $subsidy2;

    private Authenticatable $implementationCoordinatorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy1 = Subsidy::factory()->create();
        $this->subsidy2 = Subsidy::factory()->create();

        $this->implementationCoordinatorUser = User::factory()->create();
    }

    public function testSubsidyList(): void
    {
        $this->implementationCoordinatorUser->attachRole(RoleEnum::ImplementationCoordinator, $this->subsidy1->id);

        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/user/subsidies');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function testSubsidyListForSuperUser(): void
    {
        //When a user has no subsidy attached to one of his roles, this means he as access to all subsidies.
        $this->implementationCoordinatorUser->attachRole(RoleEnum::ImplementationCoordinator, null);

        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/user/subsidies');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }
}
