<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Application\API\Services\CacheService;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageUI;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Application\API\Tests\TestCase;

/**
 * @group form
 * @group form-controller
 */
class FormControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::FORM];

    private Subsidy $subsidy;
    private SubsidyStage $subsidyStage;
    private Field $field;
    private SubsidyStageUI $subsidyStageUI;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadCustomMigrations();

        $this->subsidy = Subsidy::factory()->create(['title' => 'B']);
        $this->subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $this->subsidy->id,
            'status' => VersionStatus::Published
        ]);
        $this->subsidyStage = SubsidyStage::factory()->create([
            'subsidy_version_id' => $this->subsidyVersion->id,
            'subject_role' => 'applicant'
        ]);

        $this->field = Field::factory()
            ->for($this->subsidyStage)
            ->create();

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

        $this->subsidyStageUI = SubsidyStageUI::factory()->create([
            'subsidy_stage_id' => $this->subsidyStage->id,
            'status' => VersionStatus::Published,
            'input_ui' => $ui,
            'view_ui' => $ui
        ]);

        $this->app->get(CacheService::class)->cacheSubsidyStage($this->subsidyStage);
    }

    public function testShowFormShouldReturnTheFormSchema(): void
    {
        $response = $this->getJson(route('api.form-show', $this->subsidyStage->id));
        $this->assertEquals(200, $response->status());

        $response->assertJsonPath('metadata.id', $this->subsidyStage->id);
        $response->assertJsonPath('metadata.subsidy.id', $this->subsidy->id);
        $response->assertJsonPath('metadata.subsidy.title', $this->subsidy->title);

        $response->assertJsonCount(1, 'dataschema.properties');
        $response->assertJsonPath('dataschema.properties.' . $this->field->code . '.type', 'string');
        $response->assertJsonPath('dataschema.properties.' . $this->field->code . '.title', $this->field->title);

        $response->assertJsonCount(1, 'uischema.elements');
        $response->assertJsonPath('uischema.elements.0.elements.0.scope', '#/properties/' . $this->field->code);
    }

    public function testShowFormShouldReturnA404IfNotFound(): void
    {
        $response = $this->getJson(route('api.form-show', $this->faker->uuid));
        $this->assertEquals(404, $response->status());
    }
}
