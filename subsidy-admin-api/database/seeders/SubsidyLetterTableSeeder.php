<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubsidyLetterTableSeeder extends Seeder
{
    public const BTV_LETTER_UUID = '0cf71600-0793-406e-ad19-1bb7b8c665af';


    public function run(): void
    {
        DB::table('subsidy_letters')->insert([
            'id' => self::BTV_LETTER_UUID,
            'subsidy_version_id' => SubsidyVersionsTableSeeder::BTV_VERSION_UUID,
            'version' => 1,
            'status' => "published", //TODO should be an enum
            'created_at' => '2019-02-01',
            'content_pdf' => file_get_contents(__DIR__ . '/resources/btv/btv-letter-pdf.latte'),
            'content_view' => file_get_contents(__DIR__ . '/resources/btv/btv-letter-view.latte'),
        ]);

    }
}
