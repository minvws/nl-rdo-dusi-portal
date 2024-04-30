<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentStageUISeeder extends Seeder
{
    public const SUBSIDY_STAGE2_UI_UUID = '4aa24ca1-0fa8-45d3-a632-15fd788fbc6e';
    public const SUBSIDY_STAGE3_UI_UUID = '8f7b2a5f-050e-4dd2-9d05-4e1d20f3929a';
    public const SUBSIDY_STAGE4_UI_UUID = '6a669ec1-e949-40d8-bbc4-946665553fb1';
    public const SUBSIDY_STAGE5_UI_UUID = '6b9e3359-8c44-4bfd-a593-baa5c4b8d19d';
    public const SUBSIDY_STAGE6_UI_UUID = '2a227775-700d-4f59-9322-900bb326afff';
    public const SUBSIDY_STAGE7_UI_UUID = '9b0a617b-25dd-474c-bc0a-912c503a35e8';
    public const SUBSIDY_STAGE8_UI_UUID = '9fb35125-318e-4426-8857-facefdd94fee';

    public function run(): void
    {
        $this->firstAssessment();
        $this->auditAssessment();
        $this->implementationAssessment();
        $this->assignationDelayPeriod();
        $this->assignationAssessment();
        $this->assignationAuditAssessment();
        $this->assignationImplementationAssessment();
    }

    /**
     * @throws FileNotFoundException
     */
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
        $view_ui = $this->buildSchema('view_ui/stage2_assessment.json');
        $input_ui = $this->buildSchema('input_ui/stage2_assessment.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::SUBSIDY_STAGE2_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_2_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function auditAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage3_audit.json');
        $input_ui = $this->buildSchema('input_ui/stage3_audit.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::SUBSIDY_STAGE3_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_3_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }

    public function implementationAssessment(): void
    {
        $view_ui = $this->buildSchema('view_ui/stage4_implementation.json');
        $input_ui = $this->buildSchema('input_ui/stage4_implementation.json');

        DB::table('subsidy_stage_uis')->insert([
            'id' => self::SUBSIDY_STAGE4_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_4_UUID,
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
            'id' => self::SUBSIDY_STAGE5_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_5_UUID,
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
            'id' => self::SUBSIDY_STAGE6_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_6_UUID,
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
            'id' => self::SUBSIDY_STAGE7_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_7_UUID,
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
            'id' => self::SUBSIDY_STAGE8_UI_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_8_UUID,
            'version' => 1,
            'status' => 'published',
            'input_ui' => json_encode($input_ui),
            'view_ui' => json_encode($view_ui)
        ]);
    }
}
