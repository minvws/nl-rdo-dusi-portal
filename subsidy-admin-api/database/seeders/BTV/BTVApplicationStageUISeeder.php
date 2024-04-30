<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AbstractApplicationStageUISeeder;

class BTVApplicationStageUISeeder extends AbstractApplicationStageUISeeder
{
    public const BTV_STAGE1_V1_UUID = '72475863-7987-4375-94d7-21e04ff6552b';
    public string $resourceDir = __DIR__;

    public function run(): void
    {
        $inputUI = $this->buildInputUI();

        $viewUI = $this->buildViewUI();

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE1_V1_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_1_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($inputUI),
            'view_ui' => json_encode($viewUI)
        ]);
    }

    public function buildInputUI(): array
    {
        return [
            'type' => 'CustomPageNavigationControl',
            'elements' => [
                $this->buildInputUiStep(
                    1, 'start', [
                    ]
                ),
                $this->buildInputUiStep(
                    2, 'Persoonsgegevens toevoegen', [
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
                    3, 'Documenten toevoegen', [
                        "extractPopulationRegisterDocument",
                        "proofOfMedicalTreatmentDocument",
                        "proofOfTypeOfMedicalTreatmentDocument"
                    ]
                ),
                $this->buildInputUiStep(
                    4, 'Controleren en ondertekenen', [
                        'truthfullyCompleted'
                    ]
                )
            ]
        ];
    }
}
