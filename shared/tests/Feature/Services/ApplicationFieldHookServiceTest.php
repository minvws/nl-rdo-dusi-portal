<?php

declare(strict_types=1);

namespace Feature\Services;

use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFieldHookService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Tests\TestCase;

/**
 * @group validation
 */
class ApplicationFieldHookServiceTest extends TestCase
{
    use WithFaker;

    private const SUBSIDY_DAMU_UUID = '7b9f1318-4c38-4fe5-881b-074729d95abf';

    private ApplicationFieldHookService $fieldHookService;
    private ApplicationStage $applicationStage;


    public function setUp(): void
    {
        parent::setUp();

        $this->fieldHookService = $this->app->get(ApplicationFieldHookService::class);

        $subsidy = Subsidy::factory()->create([
            'id' => self::SUBSIDY_DAMU_UUID
        ]);
        $subsidyVersion = SubsidyVersion::factory()
            ->for($subsidy)
            ->create(['status' => VersionStatus::Published]);
        $subsidyStage = SubsidyStage::factory()
            ->for($subsidyVersion)
            ->create();
        $application = Application::factory()
            ->for($subsidyVersion)
            ->create();
        $this->applicationStage = ApplicationStage::factory()
            ->for($application)
            ->for($subsidyStage)
            ->create();
    }

    public static function fieldHookTotalIncomeDataProvider(): array
    {
        return [
            'hasAlimony: Ja, Alimony: 1000, income1: 1000, income2: 2000, expectedIncome: 4000' => [
                'hasAlimony' => 'Ja',
                'alimonyAmount' => 1000,
                'income1' => 1000,
                'income2' => 2000,
                'expectedIncome' => 4000,
            ],
            'hasAlimony: Nee, Alimony: 1000, income1: 1000, income2: 2000, expectedIncome: 3000' => [
                'hasAlimony' => 'Nee',
                'alimonyAmount' => 1000,
                'income1' => 1000,
                'income2' => 2000,
                'expectedIncome' => 3000,
            ],
            'hasAlimony: Nee, Alimony: null, income1: 1000, income2: null, expectedIncome: 1000' => [
                'hasAlimony' => 'Nee',
                'alimonyAmount' => null,
                'income1' => 1000,
                'income2' => null,
                'expectedIncome' => 1000,
            ]
        ];
    }

    /**
     * @group field-hook-service
     * @dataProvider fieldHookTotalIncomeDataProvider
     */
    public function testCalculateIncomeField(
        string $hasAlimony,
        ?int $alimonyAmount,
        int $income1,
        ?int $income2,
        int $expectedIncome
    ): void {
        $fieldValues = [
            'hasAlimony' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                        'code' => 'hasAlimony',
                        'type' => FieldType::Select,
                        'params' => [
                            'options' => ['Ja', 'Nee']
                        ]
                    ]),
                value: $hasAlimony,
            ),
            'alimonyAmount' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                        'code' => 'alimonyAmount',
                        'type' => FieldType::TextNumeric,
                    ]),
                value: $alimonyAmount,
            ),
            'yearlyIncomeParent1' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'yearlyIncomeParent1',
                       'type' => FieldType::TextNumeric,
                    ]),
                value: $income1,
            ),
            'yearlyIncomeParent2' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'yearlyIncomeParent2',
                       'type' => FieldType::TextNumeric,
                    ]),
                value: $income2,
            ),
            'yearlyIncomeTotal' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'yearlyIncomeTotal',
                       'type' => FieldType::TextNumeric,
                    ]),
                value: null
            ),
        ];

        $processedFieldValues = $this->fieldHookService->findAndExecuteHooks($fieldValues, $this->applicationStage);

        $this->assertEquals($expectedIncome, $processedFieldValues['yearlyIncomeTotal']->value);
    }
    public static function fieldHookReimbursementDataProvider(): array
    {
        return [
            'income1: 1000, income2: 2000, education: Primair, travel distance: 29.1, expectedReimbursement: 4000' => [
                'income1' => 10000,
                'income2' => 20000,
                'educationType' => 'Primair onderwijs',
                'travelDistanceSingleTripe' => 29.4,
                'expectedReimbursement' => 1333.58,
            ],
            'income1: 2000, income2: 2000, education: Primair, travel distance: 29.1, expectedReimbursement: 4000' => [
                'income1' => 20000,
                'income2' => 20000,
                'educationType' => 'Primair onderwijs',
                'travelDistanceSingleTripe' => 29.4,
                'expectedReimbursement' => 1111.32,
            ],
            'income1: 2000, income2: 2000, education: Voortgezet,
             travel distance: 29.1, expectedReimbursement: 4000' => [
                'income1' => 20000,
                'income2' => 20000,
                'educationType' => 'Voortgezet onderwijs',
                'travelDistanceSingleTripe' => 29.4,
                'expectedReimbursement' => 1222.45,
            ],
        ];
    }

    /**
     * @group field-hook-service
     * @dataProvider fieldHookReimbursementDataProvider
     */
    public function testCalculateTravelExpenseReimbursementField(
        int $income1,
        ?int $income2,
        string $educationType,
        ?float $travelDistanceSingleTripe,
        float $requestedSubsidyAmount
    ): void {


        $fieldValues = [
            'hasAlimony' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                        'code' => 'hasAlimony',
                        'type' => FieldType::Select,
                        'params' => [
                            'options' => ['Ja', 'Nee']
                        ]
                    ]),
                value: 'Nee',
            ),
            'yearlyIncomeParent1' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'yearlyIncomeParent1',
                       'type' => FieldType::TextNumeric,
                    ]),
                value: $income1,
            ),
            'yearlyIncomeParent2' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'yearlyIncomeParent2',
                       'type' => FieldType::TextNumeric,
                    ]),
                value: $income2,
            ),
            'educationType' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'educationType',
                       'type' => FieldType::Select,
                        'params' => [
                            'options' => ['Primair onderwijs', 'Voortgezet onderwijs']
                        ]
                    ]),
                value: $educationType
            ),
            'travelDistanceSingleTrip' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'travelDistanceSingleTrip',
                       'type' => FieldType::TextFloat,
                    ]),
                value: $travelDistanceSingleTripe,
            ),
            'travelExpenseReimbursement' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'travelExpenseReimbursement',
                       'type' => FieldType::TextFloat,
                    ]),
                value: null,
            ),
            'requestedSubsidyAmount' => new FieldValue(
                Field::factory()
                    ->for($this->applicationStage->subsidyStage)
                    ->create([
                       'code' => 'requestedSubsidyAmount',
                       'type' => FieldType::TextFloat,
                    ]),
                value: null,
            ),
        ];

        $processedFieldValues = $this->fieldHookService->findAndExecuteHooks($fieldValues, $this->applicationStage);

        $this->assertEquals($requestedSubsidyAmount, $processedFieldValues['requestedSubsidyAmount']->value);
    }
}
