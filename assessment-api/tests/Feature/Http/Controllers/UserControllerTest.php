<?php

declare(strict_types=1);

namespace Feature\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Test\MocksEncryption;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @group user
 */
class UserControllerTest extends TestCase
{
    use MocksEncryption;
    use WithFaker;

    private Subsidy $subsidy;
    private SubsidyVersion $subsidyVersion;
    private SubsidyStage $subsidyStage1;
    private SubsidyStageHash $bankAccountSubsidyStageHash;
    private Identity $identity;
    private Application $application;

    private Authenticatable $implementationCoordinatorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create();


        $this->implementationCoordinatorUser = User::factory()->create();
        $this->implementationCoordinatorUser->attachRole(RoleEnum::ImplementationCoordinator, $this->subsidy->id);
    }

    public function testSubsidyList(): void
    {
        $response = $this
            ->be($this->implementationCoordinatorUser)
            ->json('GET', '/api/user/subsidies');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}
