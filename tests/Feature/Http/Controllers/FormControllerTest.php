<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Connection;
use App\Models\Field;
use App\Models\Form;
use App\Models\FormStatus;
use App\Models\Subsidy;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WipesSubsidyDefinitions;

/**
 * @group form
 * @group form-controller
 */
class FormControllerTest extends TestCase
{
    protected array $connectionsToTransact = [Connection::Form];

    use DatabaseTransactions;
    use WipesSubsidyDefinitions;
    use WithFaker;

    private Subsidy $subsidy;
    private Form $form;
    private Field $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create();
        $this->form = Form::factory()->create(['subsidy_id' => $this->subsidy->id, 'status' => FormStatus::Published]);
        $this->field = Field::factory()->create(['form_id' => $this->form->id]);

        $this->app->get(CacheService::class)->cacheForm($this->form);
    }

    public function testShowFormShouldReturnTheFormSchema(): void
    {
        $response = $this->getJson(route('api.form-show', $this->form->id));
        $this->assertEquals(200, $response->status());

        $response->assertJsonPath('metadata.id', $this->form->id);
        $response->assertJsonPath('metadata.subsidy.id', $this->subsidy->id);
        $response->assertJsonPath('metadata.subsidy.title', $this->subsidy->title);

        $response->assertJsonCount(1, 'dataSchema.properties');
        $response->assertJsonpath('dataSchema.properties.' . $this->field->id . '.type', 'string');
        $response->assertJsonpath('dataSchema.properties.' . $this->field->id . '.title', $this->field->label);

        $response->assertJsonCount(1, 'uiSchema.elements');
        $response->assertJsonpath('uiSchema.elements.0.scope', '#/properties/' . $this->field->id);
    }

    public function testShowFormShouldReturnA404IfNotFound(): void
    {
        $response = $this->getJson(route('api.form-show', $this->faker->uuid));
        $this->assertEquals(404, $response->status());
    }
}
