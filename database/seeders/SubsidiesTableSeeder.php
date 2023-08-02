<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

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
            'title' => 'Borstprothesen transvrouwen',
            'description' => "Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens ('genderdysforie') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.",
            'valid_from' => '2019-02-01',
            'valid_to' => null
        ]);
        Subsidy::factory()->count(2)
            ->create()
            ->each(function($subsidy){
                SubsidyVersion::factory(['subsidy_id' => $subsidy->id, 'status' => VersionStatus::Published])
                    ->create()
                    ->each(function($subsidyVersion){
                    SubsidyStage::factory(['subsidy_version_id' => $subsidyVersion->id])->create();
                });
            });
    }
}
