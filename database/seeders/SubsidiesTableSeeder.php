<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubsidiesTableSeeder extends Seeder
{
    public const BTV_UUID = '00f26400-7232-475f-922c-6b569b7e421a';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidies')->insert([
            'id' => self::BTV_UUID,
            'title' => 'BTV'
        ]);
    }
}
