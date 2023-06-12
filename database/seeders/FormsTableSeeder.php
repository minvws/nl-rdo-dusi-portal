<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormsTableSeeder extends Seeder
{
    public const BTV_V1_UUID = '29a444d8-0f36-4266-8881-489f7cfd2b1c';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('forms')->insert([
            'id' => self::BTV_V1_UUID,
            'subsidy_id' => SubsidiesTableSeeder::BTV_UUID,
            'version' => 1,
            'status' => 'published'
        ]);
    }
}
