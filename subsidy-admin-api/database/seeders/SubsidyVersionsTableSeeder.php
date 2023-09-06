<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubsidyVersionsTableSeeder extends Seeder
{
    public const BTV_VERSION_UUID = '907bb399-0d19-4e1a-ac75-25a864df27c6';
    public const PCZM_VERSION_UUID = '513011CD-789B-4628-BA5C-2FEE231F8959';

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
            'subsidy_page_url' => 'https://www.dus-i.nl/subsidies',
            'contact_mail_address' => 'dienstpostbus@minvws.nl',
            'mail_to_address_field_identifier' => 'email',
            'mail_to_name_field_identifier' => 'firstName;infix;lastName',
            'message_overview_subject' => 'Onderwerp voor overzicht',
        ]);
        DB::table('subsidy_versions')->insert([
            'id' => self::PCZM_VERSION_UUID,
            'subsidy_id' => SubsidiesTableSeeder::PCZM_UUID,
            'version' => 1,
            'status' => "published", //TODO should be an enum
            'created_at' => '2023-08-31',
            'subsidy_page_url' => 'https://www.dus-i.nl/subsidies/zorgmedewerkers-met-langdurige-post-covid-klachten'
        ]);
    }
}
