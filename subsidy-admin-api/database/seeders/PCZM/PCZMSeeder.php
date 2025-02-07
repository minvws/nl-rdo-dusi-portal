<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class PCZMSeeder extends Seeder
{
    public const PCZM_UUID = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';
    public const PCZM_VERSION_UUID = '513011cd-789b-4628-ba5c-2fee231f8959';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSubsidy();
        $this->createSubsidyVersion();

        $this->call(PCZMSubsidyStagesSeeder::class);

        $this->call(PCZMApplicationFieldsSeeder::class);
        $this->call(PCZMApplicationStageUISeeder::class);

        $this->call(PCZMAssessmentFieldsSeeder::class);
        $this->call(PCZMAssessmentStageUISeeder::class);

        $this->call(PCZMSubsidyStageTransitionsSeeder::class);
        $this->call(PCZMSubsidyStageTransitionMessageSeeder::class);
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
            'status' => VersionStatus::Archived,
            'created_at' => '2023-08-31',
            'subsidy_page_url' => 'https://www.dus-i.nl/subsidies/zorgmedewerkers-met-langdurige-post-covid-klachten',
            'contact_mail_address' => 'post-covid@minvws.nl',
            'mail_to_address_field_identifier' => 'email',
            'mail_to_name_field_identifier' => 'firstName;infix;lastName',
            'review_deadline' => CarbonImmutable::parse('2024-01-22')->endOfDay()->floorSecond()
        ]);
    }
}
