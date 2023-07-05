<?php

namespace Tests\Feature\Repositories;

use App\Models\Connection;
use App\Models\Field;
use App\Models\Form;
use App\Models\VersionStatus;
use App\Models\Subsidy;
use App\Repositories\FormRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WipesSubsidyDefinitions;

/**
 * @group form
 * @group form-repository
 */
class FormRepositoryTest extends TestCase
{
    protected array $connectionsToTransact = [Connection::Form];

    use DatabaseTransactions;
    use WipesSubsidyDefinitions;
    use WithFaker;

    private Subsidy $subsidy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subsidy = Subsidy::factory()->create();
    }

    public function getOpenFormsDataProvider(): array
    {
        return [
            [0, 0, 0, 0],
            [1, 0, 0, 0],
            [0, 1, 0, 1],
            [0, 0, 1, 1],
            [0, 0, 2, 2],
            [0, 1, 1, 2],
            [1, 1, 1, 2],
            [1, 1, 2, 3],
        ];
    }

    /**
     * @dataProvider getOpenFormsDataProvider
     */
    public function testGetOpenForms(int $draftForms, int $publishedForms, int $archivedForms, int $expectedOpenForms): void
    {
        for ($i = 0; $i < $draftForms; $i++) {
            Form::factory()->create(['status' => VersionStatus::Draft, 'subsidy_id' => $this->subsidy->id]);
        }

        for ($i = 0; $i < $publishedForms; $i++) {
            Form::factory()->create(['status' => VersionStatus::Published, 'subsidy_id' => $this->subsidy->id]);
        }

        for ($i = 0; $i < $archivedForms; $i++) {
            Form::factory()->create(['status' => VersionStatus::Archived, 'subsidy_id' => $this->subsidy->id]);
        }

        $repository = $this->app->get(FormRepository::class);
        $this->assertCount($expectedOpenForms, $repository->getOpenFormsForSubsidy($this->subsidy));
    }

    public function getFormDataProvider(): array
    {
        return [
            [VersionStatus::Draft, false],
            [VersionStatus::Published, true],
            [VersionStatus::Archived, true]
        ];
    }

    /**
     * @dataProvider getFormDataProvider
     */
    public function testGetForm(VersionStatus $status, bool $expectForm): void
    {
        $form = Form::factory()->create(['status' => $status, 'subsidy_id' => $this->subsidy->id]);

        $repository = $this->app->get(FormRepository::class);
        $form = $repository->getForm($form->id);
        $this->assertEquals($expectForm, $form !== null);
        if ($expectForm) {
            assert($form instanceof Model);
            $this->assertTrue($form->relationLoaded('fields'));
        }
    }

    public function testGetFormInvalidId(): void
    {
        $repository = $this->app->get(FormRepository::class);
        $this->assertNull($repository->getForm($this->faker->uuid));
    }
}
