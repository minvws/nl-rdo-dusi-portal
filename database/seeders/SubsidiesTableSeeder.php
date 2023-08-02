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
            'title' => 'Borstprothesen transvrouwen',
            'description' => "Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens ('genderdysforie') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.",
            'valid_from' => '2019-02-01',
            'valid_to' => null
        ]);
        DB::table('subsidies')->insert([
            'id' => fake()->uuid(),
            'title' => 'Alternative subsidie',
            'description' => "Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens ('genderdysforie') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.",
            'valid_from' => '2020-02-01',
            'valid_to' => null
        ]);
        DB::table('subsidies')->insert([
            'id' => fake()->uuid(),
            'title' => 'Subsidie die niet bestaat',
            'description' => "Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens ('genderdysforie') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.",
            'valid_from' => '2021-02-01',
            'valid_to' => null
        ]);
    }
}
