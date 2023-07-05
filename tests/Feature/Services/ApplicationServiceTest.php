<?php
declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\FieldType;
use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\Subsidy;
use App\Shared\Models\Definition\VersionStatus;
use App\Services\ApplicationService;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\Codable\Exceptions\ValueNotFoundException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use Tests\TestCase;

class ApplicationServiceTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private Form $form;
    private Field $textField;
    private Field $numericField;

    protected function setUp(): void
    {
        parent::setUp();
        $subsidy = Subsidy::factory()->create();
        $this->form = Form::factory()->create(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Published]);
        $this->textField = Field::factory()->create(['form_id' => $this->form->id, 'type' => FieldType::Text, 'code' => 'text']);
        $this->numericField = Field::factory()->create(['form_id' => $this->form->id, 'type' => FieldType::TextNumeric, 'code' => 'number']);
    }


    public function testProcessFormSubmit(): void
    {
        $data = [
            $this->textField->code => $this->faker->word,
            $this->numericField->code => $this->faker->randomDigit(),
        ];
        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $application = $applicationService->processFormSubmit($this->form->id, json_encode($data));
        $this->assertNotNull($application);
    }

    public static function invalidFormSubmitProvider(): Generator
    {
        yield 'text-should-be-string' => [123, 123, ValueTypeMismatchException::class];
        yield 'text-should-not-be-null' => [null, 123, ValueNotFoundException::class];
        yield 'numeric-should-be-int' => ['text', 'text', ValueTypeMismatchException::class];
        yield 'numeric-should-not-be-null' => ['text', null, ValueNotFoundException::class];
    }

    /**
     * @dataProvider invalidFormSubmitProvider
     */
    public function testProcessFormSubmitInvalidFieldData(mixed $text, mixed $numeric, string $expectedException): void
    {
        $data = [
            $this->textField->code => $text,
            $this->numericField->code => $numeric,
        ];

        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException($expectedException);
        $applicationService->processFormSubmit($this->form->id, json_encode($data));
    }

    public function testProcessFormSubmitInvalidForm(): void
    {
        $applicationService = $this->app->get(ApplicationService::class);
        assert($applicationService instanceof ApplicationService);
        $this->expectException(\Exception::class);
        $applicationService->processFormSubmit($this->faker->uuid, json_encode([]));
    }
}
