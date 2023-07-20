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
//
//        $this->call(FormsTableSeeder::class);
//        $this->call(FieldsTableSeeder::class);
//        $this->call(FormUIsTableSeeder::class);
        //$this->call(FormHashesTableSeeder::class);
        //$this->call(FormHashFieldsTableSeeder::class);
    }
}
