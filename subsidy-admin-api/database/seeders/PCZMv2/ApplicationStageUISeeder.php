<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2;

use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AbstractApplicationStageUISeeder;

class ApplicationStageUISeeder extends AbstractApplicationStageUISeeder
{
    public const STAGE1_V1_UUID = '422cb32a-fed3-4d69-91ca-818db6b96daf';

    public string $resourceDir = __DIR__;

    public function run(): void
    {
        $inputUi = $this->buildInputUi();
        $viewUI = $this->buildViewUI();

        DB::table('subsidy_stage_uis')->insert(
            [
                'id' => self::STAGE1_V1_UUID,
                'subsidy_stage_id' => SubsidyStagesSeeder::STAGE_1_UUID,
                'version' => 1,
                'status' => 'published',
                'input_ui' => json_encode($inputUi),
                'view_ui' => json_encode($viewUI),
            ]
        );
    }

    public function buildInputUi(): array
    {
        return [
            'type' => 'CustomPageNavigationControl',
            'elements' => [
                $this->buildInputUiStep(
                    1,
                    'Start',
                    [
                    ]
                ),
                $this->buildInputUiStep(
                    2,
                    'Persoonsgegevens toevoegen',
                    [
                        "firstName",
                        "lastName",
                        "street",
                        "dateOfBirth",
                        "houseNumber",
                        "postalCode",
                        "city",
                        "country",
                        "phoneNumber",
                        "email",
                        "bankAccountHolder",
                        "bankAccountNumber",
                    ]
                ),
                $this->buildInputUiStep(
                    3,
                    'Documenten toevoegen',
                    [
                        "certifiedEmploymentDocument",
                        "wiaDecisionDocument",
                        "isWiaDecisionPostponed",
                        "isWiaDecisionFirstSickDayOutsidePeriod",
                        "employmentContract",
                        "employmentFunction",
                        "employerKind",
                        "hasBeenWorkingAtJudicialInstitution",
                        "socialMedicalAssessment",
                        "hasPostCovidDiagnose",
                    ]
                ),
                $this->buildInputUiStep(
                    4,
                    'Controleren en ondertekenen',
                    [
                        'truthfullyCompleted',
                    ]
                ),
            ],
        ];
    }
}
