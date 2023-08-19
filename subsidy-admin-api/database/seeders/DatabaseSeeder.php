<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SubsidiesTableSeeder::class);
        $this->call(SubsidyVersionsTableSeeder::class);
        $this->call(SubsidyStagesTableSeeder::class);
        $this->call(FieldsTableSeeder::class);
        $this->call(BTVUIFormTableSeeder::class);
        $this->call(BTVUIAssessmentTableSeeder::class);
    }
}
