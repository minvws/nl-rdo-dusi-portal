<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;

class SubsidiesTableSeeder extends Seeder
{
    public const BTV_UUID = '00f26400-7232-475f-922c-6b569b7e421a';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        DB::table('subsidies')->insert([
//            'id' => self::BTV_UUID,
//            'title' => 'Borstprothesen transvrouwen',
//            'reference_prefix' => 'BTV23',
//            'code' => 'BTV',1
//            'description' => "Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens ('genderdysforie') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.",
//            'valid_from' => '2019-02-01',
//            'valid_to' => null
//        ]);
//        Subsidy::factory()->count(2)
//            ->create()
//            ->each(function ($subsidy){
//                SubsidyVersion::factory(
//                    [
//                        'subsidy_id' => $subsidy->id,
//                        'status' => VersionStatus::Published,
//                        'subsidy_page_url' => 'https://www.dus-i.nl/subsidies',
//                        'contact_mail_address' => 'dienstpostbus@minvws.nl',
//                        'mail_to_address_field_identifier' => 'email',
//                        'mail_to_name_field_identifier' => 'firstName;infix;lastName'
//                    ]
//                )
//                    ->create()
//                    ->each(function($subsidyVersion){
//                    SubsidyStage::factory(['subsidy_version_id' => $subsidyVersion->id])->create();
//                });
//            });
    }
}
