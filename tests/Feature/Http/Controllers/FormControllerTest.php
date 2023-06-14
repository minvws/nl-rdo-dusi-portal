<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Connection;
use App\Models\Form;
use App\Models\FormStatus;
use App\Models\Subsidy;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WipesSubsidyDefinitions;

class FormControllerTest extends TestCase
{
    protected array $connectionsToTransact = [Connection::Form];

    use DatabaseTransactions;
    use WipesSubsidyDefinitions;
    use WithFaker;

    private Form $form;

    protected function setUp(): void
    {
        parent::setUp();

        $subsidy = Subsidy::factory()->create();
        $this->form = Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => FormStatus::Published]);

        $this->app->get(CacheService::class)->cacheForm($this->form);
    }

    public function testShowFormShouldReturnTheFormSchema(): void
    {
        $response = $this->getJson(route('api.form-show', $this->form->id));
        $this->assertEquals(200, $response->status());
    }


    public function testShowFormShouldReturnA404IfNotFound(): void
    {
        $response = $this->getJson(route('api.form-show', $this->faker->uuid));
        $this->assertEquals(404, $response->status());
    }
}
