<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SubsidiesTableSeeder::class);
        $this->call(SubsidyVersionsTableSeeder::class);
        $this->call(SubsidyStagesTableSeeder::class);
        $this->call(SubsidyStageTransitionsTableSeeder::class);
        $this->call(SubsidyStageTransitionMessagesTableSeeder::class);

//        $this->call(BTVFieldsTableSeeder::class);
//        $this->call(BTVUIFormTableSeeder::class);
//        $this->call(BTVUIAssessmentTableSeeder::class);

        $this->call(PCZMApplicationFieldsTableSeeder::class);
        $this->call(PCZMApplicationStageUITableSeeder::class);
        $this->call(PCZMAssessmentFieldsTableSeeder::class);
        $this->call(PCZMAssessmentStageUITableSeeder::class);
    }
}
