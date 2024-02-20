<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AbstractApplicationStageUISeeder;

class ApplicationStageUISeeder extends AbstractApplicationStageUISeeder
{
    public const SUBSIDY_STAGE1_V1_UUID = '491a4071-c401-4041-97fd-39b8c3aa70c8';

    public function run(): void
    {
        $this->resourceDir = __DIR__;

        $inputUI = $this->buildInputUI();

        $viewUI = $this->buildViewUI();

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::SUBSIDY_STAGE1_V1_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
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
                    3, 'Opleidingsgegevens', [
                        "abroadCourseComponentStartDate",
                        "proofOfRegistrationDocument",
                        "educationalInstituteDeclarationDocument",
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
