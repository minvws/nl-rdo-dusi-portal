<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\Enums\VersionStatus;
use App\Models\Form;
use App\Models\Subsidy;
use App\Repositories\SubsidyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group form
 * @group form-repository
 */
class SubsidyRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function testGetSubsidy(): void
    {
        $subsidy = Subsidy::factory()->create();
        $form = Form::factory()->create(['status' => VersionStatus::Published, 'subsidy_id' => $subsidy->id]);
        $repository = $this->app->get(SubsidyRepository::class);
        $form = $repository->getSubsidy($form->subsidy_id);
        $this->assertNotNull($form);
        $this->assertTrue($form->relationLoaded('forms'));
    }
}
