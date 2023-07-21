<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\Connection;
use App\Models\Enums\VersionStatus;
use App\Models\SubsidyStage;
use App\Models\Subsidy;
use App\Models\SubsidyVersion;
use App\Repositories\SubsidyRepository;
use Database\Factories\SubsidyVersionFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group form
 * @group form-repository
 */
class SubsidyRepositoryTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::FORM];

    public function testGetSubsidy(): void
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create(
            [
                'status' => VersionStatus::Published,
                'subsidy_id' => $subsidy->id,
                'version' => 1,
            ]
        );
        $subsidyStage = SubsidyStage::factory()->create(
            [
                'subsidy_version_id' => $subsidyVersion->id,
                'stage' => 1
            ]
        );

//        $repository = $this->app->get(SubsidyRepository::class);
//        $form = $repository->getSubsidy($form->subsidy_id);
//        $this->assertNotNull($form);
//        $this->assertTrue($form->relationLoaded('forms'));
    }
}
