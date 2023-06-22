<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Connection;
use App\Models\Form;
use App\Models\FormStatus;
use App\Models\Subsidy;
use App\Repositories\SubsidyRepository;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WipesSubsidyDefinitions;

class SubsidyControllerTest extends TestCase
{
    protected array $connectionsToTransact = [Connection::Form];

    use DatabaseTransactions;
    use WipesSubsidyDefinitions;
    use WithFaker;

    private Subsidy $subsidy1;
    private Subsidy $subsidy2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy1 = Subsidy::factory()->create(['title' => 'B']);
        Form::factory()->create(['subsidy_id' => $this->subsidy1->id, 'status' => FormStatus::Published]);

        $this->subsidy2 = Subsidy::factory()->create(['title' => 'A']);
        Form::factory()->create(['subsidy_id' => $this->subsidy2->id, 'status' => FormStatus::Published]);

        $activeSubsidies = $this->app->get(SubsidyRepository::class)->getActiveSubsidies();
        $this->app->get(CacheService::class)->cacheActiveSubsidies($activeSubsidies);
    }

    public function testActiveSubsidies(): void
    {
        $response = $this->getJson(route('api.subsidy-list'));
        $this->assertEquals(200, $response->status());

        // check amount
        $response->assertJsonCount(2);

        // check order
        $response->assertJsonPath('0.title', $this->subsidy2->title);
        $response->assertJsonPath('1.title', $this->subsidy1->title);

        // check form link
        $this->assertEquals(route('api.form-show', $this->subsidy2->publishedForm->id), $response->json('0._links.form.href'));
    }
}
