<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BTVAssessmentStageUISeeder extends Seeder
{
    public const BTV_STAGE2_UI_UUID = 'db1076a1-42f3-4c90-b1bf-57d1db025f2e';
    public const BTV_STAGE3_UI_UUID = '787c8ef4-bfcd-4bd4-aec1-dec02139c897';
    public const BTV_STAGE4_UI_UUID = 'a6080627-0ea9-436e-bbba-c454bd3809fd';
    public const BTV_STAGE5_UI_UUID = 'ef196de1-5c15-4af3-9ec8-046ca4419fd1';
    public const BTV_STAGE6_UI_UUID = 'd15ff747-b912-4abc-b6df-2a750c820d92';
    public const BTV_STAGE7_UI_UUID = 'fe5b6562-9cf9-4c2f-b963-dadc87044766';
    public const BTV_STAGE8_UI_UUID = '08cdcb36-d618-4e89-8fb3-778e66a3bf2a';

    public function run(): void
    {
        $this->firstAssessment();
        $this->secondAssessment();
        $this->internalAssessment();

        $this->assignationDelayPeriod();
        $this->assignationAssessment();
        $this->assignationAuditAssessment();
        $this->assignationImplementationAssessment();
    }

    private function buildSchema(string $path): array
    {
        $filePath = __DIR__ . sprintf('/resources/%s', $path);
        if (!file_exists($filePath)) {
            throw new FileNotFoundException("Unable to open file for schema path: {$path}");
        }
        $json = file_get_contents($filePath);
        assert(is_string($json));
        return json_decode($json, true);
    }

    public function firstAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage2.json');
        $input_ui = $this->buildSchema('input_ui/stage2_assessment.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE2_UI_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function secondAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage3.json');
        $input_ui = $this->buildSchema('input_ui/stage3_second_assessment.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE3_UI_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function internalAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage4.json');
        $input_ui = $this->buildSchema('input_ui/stage4_internal_assessment.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE4_UI_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
    public function assignationDelayPeriod(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage5_6_assignation_delay_period.json');
        $input_ui = $this->buildSchema('input_ui/stage5_6_assignation_delay_period.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE5_UI_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function assignationAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage5_6_assignation_delay_period.json');
        $input_ui = $this->buildSchema('input_ui/stage5_6_assignation_delay_period.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE6_UI_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function assignationAuditAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage7_assignation_audit_assessment.json');
        $input_ui = $this->buildSchema('input_ui/stage7_assignation_audit_assessment.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE7_UI_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function assignationImplementationAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage8_assignation_implementation_assessment.json');
        $input_ui = $this->buildSchema('input_ui/stage8_assignation_implementation_assessment.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::BTV_STAGE8_UI_UUID,
            'subsidy_stage_id' => BTVSubsidyStagesSeeder::BTV_STAGE_8_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
