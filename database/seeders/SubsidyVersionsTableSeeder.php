<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubsidyVersionsTableSeeder extends Seeder
{
    public const BTV_VERSION_UUID = '907bb399-0d19-4e1a-ac75-25a864df27c6';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidy_versions')->insert([
            'id' => self::BTV_VERSION_UUID,
            'subsidy_id' => SubsidiesTableSeeder::BTV_UUID,
            'version' => 1,
            'status' => "published", //TODO should be an enum
            'created_at' => '2019-02-01',
        ]);
    }
}
