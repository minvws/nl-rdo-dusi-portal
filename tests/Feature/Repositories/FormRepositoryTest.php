<?php

namespace Tests\Feature\Repositories;

use App\Models\Connection;
use App\Models\Definition\Form;
use App\Models\Definition\FormStatus;
use App\Models\Definition\Subsidy;
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
    protected array $connectionsToTransact = [Connection::Form];

    use DatabaseTransactions;
    use WithFaker;

    public function testGetForm(): void
    {
        $subsidy = Subsidy::factory()->create();
        $form = Form::factory()->create(['status' => FormStatus::Published, 'subsidy_id' => $subsidy->id]);

        $repository = $this->app->get(FormRepository::class);
        $form = $repository->getForm($form->id);
        $this->assertNotNull($form);
        $this->assertTrue($form->relationLoaded('fields'));
    }
}
