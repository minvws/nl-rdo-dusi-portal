<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class SubsidiesTableSeeder extends Seeder
{
    public const BTV_UUID = '00f26400-7232-475f-922c-6b569b7e421a';
    public const PCZM_UUID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subsidies')->insert([
            'id' => self::BTV_UUID,
            'title' => 'Borstprothesen transvrouwen',
            'reference_prefix' => 'BTV23',
            'code' => 'BTV',
            'description' => "Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens ('genderdysforie') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.",
            'valid_from' => '2019-02-01',
            'valid_to' => null
        ]);
        DB::table('subsidies')->insert([
            'id' => self::PCZM_UUID,
            'title' => 'Aanvraagformulier financiële ondersteuning Zorgmedewerkers met langdurige post-COVID klachten',
            'reference_prefix' => 'PCZM23',
            'code' => 'PCZM',
            'description' => "De regeling Zorgmedewerkers met langdurige post-COVID klachten richt zich op zorgmedewerkers die tijdens de eerste golf van de COVID-19 pandemie besmet zijn geraakt met COVID-19 en sindsdien langdurige post-COVID klachten hebben. Deze klachten hebben grote invloed op het werk en het privéleven van deze zorgmedewerkers. Zij kunnen soms hun eigen werk als zorgmedewerker niet meer (volledig) doen. Voor deze specifieke groep zorgmedewerkers is een eenmalige financiële ondersteuning van €15.000 beschikbaar.",
            'valid_from' => '2023-09-01',
            'valid_to' => null
        ]);
        Subsidy::factory()->count(2)
            ->create()
            ->each(function ($subsidy){
                SubsidyVersion::factory(
                    [
                        'subsidy_id' => $subsidy->id,
                        'status' => VersionStatus::Published,
                        'subsidy_page_url' => 'https://www.dus-i.nl/subsidies',
                        'contact_mail_address' => 'dienstpostbus@minvws.nl',
                        'mail_to_address_field_identifier' => 'email',
                        'mail_to_name_field_identifier' => 'firstName;infix;lastName',
                        'message_overview_subject' => 'Onderwerp voor overzicht'
                    ]
                )
                    ->create()
                    ->each(function($subsidyVersion){
                    SubsidyStage::factory(['subsidy_version_id' => $subsidyVersion->id])->create();
                });
            });
    }
}
