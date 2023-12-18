<?php

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\SubsidiesTableSeeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\SubsidyVersionsTableSeeder;

class PCZMSeeder extends Seeder
{
    public const PCZM_UUID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';
    public const PCZM_VERSION_UUID = '513011cd-789b-4628-ba5c-2fee231f8959';

    public const PCZM_LETTER_UUID = 'c51302f6-e131-45ff-8d4b-f4ff4a39b52f';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSubsidy();
        $this->createSubsidyVersion();


        $this->call(PCZMSubsidyStagesSeeder::class);
        $this->call(PCZMSubsidyStageTransitionsSeeder::class);

        //$this->createSubsidyLetters();
        $this->call(PCZMSubsidyStageTransitionMessageSeeder::class);

        $this->call(PCZMApplicationFieldsTableSeeder::class);
        $this->call(PCZMApplicationStageUITableSeeder::class);
        $this->call(PCZMAssessmentFieldsTableSeeder::class);
        $this->call(PCZMAssessmentStageUITableSeeder::class);
    }

    public function createSubsidy(): void
    {
        DB::table('subsidies')->insert([
                                           'id' => self::PCZM_UUID,
                                           'title' => 'Zorgmedewerkers met langdurige post-COVID klachten',
                                           'reference_prefix' => 'PCZM23',
                                           'code' => 'PCZM',
                                           'description' => "De regeling Zorgmedewerkers met langdurige post-COVID klachten richt zich op zorgmedewerkers die tijdens de eerste golf van de COVID-19 pandemie besmet zijn geraakt met COVID-19 en sindsdien langdurige post-COVID klachten hebben. Deze klachten hebben grote invloed op het werk en het privéleven van deze zorgmedewerkers. Zij kunnen soms hun eigen werk als zorgmedewerker niet meer (volledig) doen. Voor deze specifieke groep zorgmedewerkers is een eenmalige financiële ondersteuning van €15.000 beschikbaar.",
                                           'valid_from' => '2023-09-25 09:00 CEST',
                                           'valid_to' => null,
                                       ]);
    }

    private function createSubsidyVersion(): void
    {
        DB::table('subsidy_versions')->insert([
                                                  'id' => self::PCZM_VERSION_UUID,
                                                  'subsidy_id' => self::PCZM_UUID,
                                                  'version' => 1,
                                                  'status' => "published", //TODO should be an enum
                                                  'created_at' => '2023-08-31',
                                                  'subsidy_page_url' => 'https://www.dus-i.nl/subsidies/zorgmedewerkers-met-langdurige-post-covid-klachten',
                                                  'contact_mail_address' => 'dienstpostbus@minvws.nl',
                                                  'mail_to_address_field_identifier' => 'email',
                                                  'mail_to_name_field_identifier' => 'firstName;infix;lastName',
                                                  'review_deadline' => CarbonImmutable::parse('2024-01-22')->endOfDay()->floorSecond()
                                              ]);
    }

    private function createSubsidyLetters(): void
    {
        DB::table('subsidy_letters')->insert([
                                                 'id' => self::PCZM_LETTER_UUID,
                                                 'subsidy_version_id' => self::PCZM_VERSION_UUID,
                                                 'version' => 1,
                                                 'status' => "published", //TODO should be an enum
                                                 'created_at' => '2019-02-01',
                                                 'content_pdf' => file_get_contents(__DIR__ . '/resources/pczm-letter-pdf.latte'),
                                                 'content_view' => file_get_contents(__DIR__ . '/resources/pczm-letter-view.latte'),
                                             ]);
    }
}
