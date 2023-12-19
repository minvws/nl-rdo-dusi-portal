<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AbstractApplicationStageUISeeder;
use stdClass;

class PCZMApplicationStageUISeeder extends AbstractApplicationStageUISeeder
{
    public const PCZM_STAGE1_V1_UUID = 'e6d5cd35-8c67-40c4-abc4-b1d6bf8afb97';

    public function run(): void
    {
        $this->resourceDir = __DIR__;

        $inputUi = $this->buildInputUi();
        $viewUI = $this->buildViewUI();

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::PCZM_STAGE1_V1_UUID,
            'subsidy_stage_id' => PCZMSubsidyStagesSeeder::PCZM_STAGE_1_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($inputUi),
            'view_ui' => json_encode($viewUI)
        ]);
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
                        "bankAccountNumber"
                    ]
                ),
                $this->buildInputUiStep(
                    3,
                    'Documenten toevoegen',
                    [
                        "certifiedEmploymentDocument",
                        "wiaDecisionDocument",
                        "isWiaDecisionPostponed",
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
                        'truthfullyCompleted'
                    ]
                )
            ]
        ];
    }
}
