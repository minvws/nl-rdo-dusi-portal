<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subsidy::factory()->create();
        SubsidyVersion::factory()->create(['subsidy_id' => Subsidy::all()->first()->id]);
        SubsidyStage::factory()->create(['subsidy_version_id' => SubsidyVersion::all()->first()->id]);
        Application::factory()->create(['subsidy_version_id' => SubsidyVersion::all()->first()->id]);
        ApplicationStage::factory()->create(['application_id' => Application::all()->first()->id, 'subsidy_stage_id' => SubsidyStage::all()->first()->id]);
        ApplicationStageVersion::factory()->create(['application_stage_id' => ApplicationStage::all()->first()->id]);
        Answer::factory()->create(['application_stage_version_id' => ApplicationStageVersion::all()->first()->id]);
    }
}
