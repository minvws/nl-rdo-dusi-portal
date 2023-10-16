<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Application\API\Services\CacheService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Application\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

/**
 * @group subsidy
 * @group subsidy-controller
 */
class SubsidyControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::APPLICATION];

    private Subsidy $subsidy1;
    private Subsidy $subsidy2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();

        $this->subsidy1 = Subsidy::factory()->create(['title' => 'B']);
        $this->subsidyVersion1 = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy1->id,
            'status' => VersionStatus::Published
        ]);
        SubsidyStage::factory()->create(['subsidy_version_id' => $this->subsidyVersion1->id]);

        $this->subsidy2 = Subsidy::factory()->create(['title' => 'A']);
        $this->subsidyVersion2 = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy2->id,
            'status' => VersionStatus::Published
        ]);
        SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion2->id,
            'subject_role' => 'applicant'
        ]);

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
        $response->assertJsonPath('1.validFrom', $this->subsidy1->valid_from->format('Y-m-d\TH:i:sp'));
        $response->assertJsonPath('1.validTo', $this->subsidy1->valid_to?->format('Y-m-d\TH:i:sp'));

        // check form link
        $this->assertEquals(
            route('api.form-show', $this->subsidy2->publishedVersion->subsidyStages->first()->id),
            $response->json('0._links.form.href')
        );
    }
}
