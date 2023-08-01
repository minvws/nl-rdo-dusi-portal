<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Repositories\SubsidyRepository;
use App\Services\CacheService;
use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\VersionStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WipesSubsidyDefinitions;

/**
 * @group subsidy
 * @group subsidy-controller
 */
class SubsidyControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WipesSubsidyDefinitions;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::Form];

    private Subsidy $subsidy1;
    private Subsidy $subsidy2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy1 = Subsidy::factory()->create(['title' => 'B']);
        Form::factory()->create(['subsidy_id' => $this->subsidy1->id, 'status' => VersionStatus::Published]);

        $this->subsidy2 = Subsidy::factory()->create(['title' => 'A']);
        Form::factory()->create(['subsidy_id' => $this->subsidy2->id, 'status' => VersionStatus::Published]);

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

        // check metadata
        $response->assertJsonPath('1.description', $this->subsidy1->description);
        $response->assertJsonPath('1.validFrom', $this->subsidy1->valid_from->format('Y-m-d'));
        $response->assertJsonPath('1.validTo', $this->subsidy1->valid_to?->format('Y-m-d'));

        // check form link
        $this->assertEquals(
            route('api.form-show', $this->subsidy2->publishedForm->id),
            $response->json('0._links.form.href')
        );
    }
}
