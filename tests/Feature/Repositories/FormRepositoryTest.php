<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Enums\VersionStatus;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\SubsidyStage;
use App\Repositories\FormRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group form
 * @group form-repository
 */
class FormRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::FORM];

    public function testGetForm(): void
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = $subsidy->subsidyVersions()->create([
            'version' => 1,
            'status' => VersionStatus::getDefault(),
        ]);
        $subsidyStage = SubsidyStage::factory()->create([
            'subsidy_version_id' => $subsidyVersion->id]);

        $repository = $this->app->get(FormRepository::class);
        $subsidyStage = $repository->getSubsidyStage($subsidyStage->id);
        $this->assertNotNull($subsidyStage);
    }
}
