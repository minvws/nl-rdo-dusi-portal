<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class BTVSeeder extends Seeder
{
    public const BTV_UUID = '00f26400-7232-475f-922c-6b569b7e421a';
    public const BTV_VERSION_UUID = '907bb399-0d19-4e1a-ac75-25a864df27c6';


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSubsidy();
        $this->createSubsidyVersion();
        $this->call(BTVSubsidyStagesSeeder::class);

        $this->call(BTVApplicationFieldsSeeder::class);
        $this->call(BTVApplicationStageUISeeder::class);

        $this->call(BTVAssessmentFieldsSeeder::class);
        $this->call(BTVAssessmentStageUISeeder::class);

        $this->call(BTVSubsidyStageTransitionsSeeder::class);
        $this->call(BTVSubsidyStageTransitionMessageSeeder::class);
    }

    public function createSubsidy(): void
    {
        DB::table('subsidies')->insert([
           'id' => self::BTV_UUID,
           'title' => 'Borstprothesen transvrouwen',
           'reference_prefix' => 'BTV24',
           'code' => 'BTV',
           'description' => "Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens ('genderdysforie') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.",
           'valid_from' => '2019-02-01',
           'valid_to' => null
       ]);
    }

    private function createSubsidyVersion(): void
    {
        DB::table('subsidy_versions')->insert([
            'id' => self::BTV_VERSION_UUID,
            'subsidy_id' => self::BTV_UUID,
            'version' => 1,
            'status' => VersionStatus::Published,
            'created_at' => '2019-02-01',
            'subsidy_page_url' => 'https://www.dus-i.nl/subsidies',
            'contact_mail_address' => 'dienstpostbus@minvws.nl',
            'mail_to_address_field_identifier' => 'email',
            'mail_to_name_field_identifier' => 'firstName;infix;lastName',
            'review_period' => 7 * 13 // 13 weeks
        ]);
    }
}
