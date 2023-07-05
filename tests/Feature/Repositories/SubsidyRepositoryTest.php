<?php

namespace Tests\Feature\Repositories;

use App\Repositories\SubsidyRepository;
use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\VersionStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\WipesSubsidyDefinitions;

class SubsidyRepositoryTest extends TestCase
{
    protected array $connectionsToTransact = [Connection::Form];

    use DatabaseTransactions;
    use WipesSubsidyDefinitions;

    public function testActiveSubsidiesFilteredByStatus(): void
    {
        $repository = $this->app->get(SubsidyRepository::class);

        $this->assertCount(0, $repository->getActiveSubsidies());

        // subsidy without a published form is not active
        $subsidy = Subsidy::factory()->create();
        $this->assertCount(0, $repository->getActiveSubsidies());

        // create a draft form, still not active
        $form = Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Draft]);
        $this->assertCount(0, $repository->getActiveSubsidies());

        // when there is a published form, the subsidy becomes active
        $form->status = VersionStatus::Published;
        $form->save();
        $this->assertCount(1, $repository->getActiveSubsidies());

        // until there is no published form anymore
        $form->status = VersionStatus::Archived;
        $form->save();
        $this->assertCount(1, $repository->getActiveSubsidies());

        // a new published form makes it active again
        Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Published]);
        $this->assertCount(1, $repository->getActiveSubsidies());
    }

    public function testActiveSubsidiesPublishedForm(): void
    {
        $subsidy = Subsidy::factory()->create();
        Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Archived]);
        $publishedForm = Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Published]);
        Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Draft]);

        $repository = $this->app->get(SubsidyRepository::class);
        $activeSubsidies = $repository->getActiveSubsidies();
        $this->assertCount(1, $activeSubsidies);
        $this->assertTrue(isset($activeSubsidies[0]->publishedForm));
        $this->assertEquals($publishedForm->id, $activeSubsidies[0]->publishedForm->id);
    }
}
