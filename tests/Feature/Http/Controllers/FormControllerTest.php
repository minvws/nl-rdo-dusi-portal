<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Services\CacheService;
use App\Shared\Models\Connection;
use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\FormUI;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\VersionStatus;
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
    use DatabaseTransactions;
    use WipesSubsidyDefinitions;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::Form];

    private Subsidy $subsidy;
    private Form $form;
    private Field $field;
    private FormUI $formUI;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create();
        $this->form = Form::factory()->create(
            ['subsidy_id' => $this->subsidy->id, 'status' => VersionStatus::Published]
        );
        $this->field = Field::factory()->create(['form_id' => $this->form->id]);

        $ui = [
            "type" => "CustomGroupControl",
            "options" => [
                "section" => true
            ],
            "label" => "Section",
            "elements" => [
                [
                    "type" => "VerticalLayout",
                    "elements" => [
                        [
                            "type" => "CustomControl",
                            "scope" => "#/properties/" . $this->field->code
                        ],
                    ]
                ]
            ]
        ];

        $this->ui = FormUI::factory()->create(
            ['form_id' => $this->form->id, 'status' => VersionStatus::Published, 'ui' => $ui]
        );

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
        $response->assertJsonPath('dataSchema.properties.' . $this->field->code . '.type', 'string');
        $response->assertJsonPath('dataSchema.properties.' . $this->field->code . '.title', $this->field->title);

        $response->assertJsonCount(1, 'uiSchema.elements');
        $response->assertJsonPath('uiSchema.elements.0.elements.0.scope', '#/properties/' . $this->field->code);
    }

    public function testShowFormShouldReturnA404IfNotFound(): void
    {
        $response = $this->getJson(route('api.form-show', $this->faker->uuid));
        $this->assertEquals(404, $response->status());
    }
}
