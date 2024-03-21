<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class DAMUSeeder extends Seeder
{
    public const SUBSIDY_UUID = '7b9f1318-4c38-4fe5-881b-074729d95abf';
    public const SUBSIDY_VERSION_UUID = '9a362ac7-281e-404a-b458-bdfb24f80fb0';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSubsidy();
        $this->createSubsidyVersion();
        $this->call(SubsidyStagesSeeder::class);

        $this->call(ApplicationFieldsSeeder::class);
        $this->call(ApplicationStageUISeeder::class);

        $this->call(AssessmentFieldsSeeder::class);
        $this->call(AssessmentStageUISeeder::class);

        $this->call(SubsidyStageTransitionsSeeder::class);
        $this->call(SubsidyStageTransitionMessageSeeder::class);
    }

    public function createSubsidy(): void
    {
        DB::table('subsidies')->insert([
            'id' => self::SUBSIDY_UUID,
            'title' => 'Subsidieregeling reiskosten DAMU-leerlingen',
            'reference_prefix' => 'DAMU',
            'code' => 'DAMU',
            'description' => 'Ouders of verzorgers van een DAMU-leerling op het primair of het voortgezet onderwijs kunnen een tegemoetkoming in de reiskosten aanvragen. Deze reiskosten kunnen namelijk een barrière zijn voor talentvolle leerlingen om een opleiding aan een DAMU-school te volgen.',
            'valid_from' => 'now()',
            'valid_to' => null
        ]);
    }

    private function createSubsidyVersion(): void
    {
        DB::table('subsidy_versions')->insert([
            'id' => self::SUBSIDY_VERSION_UUID,
            'subsidy_id' => self::SUBSIDY_UUID,
            'version' => 1,
            'status' => VersionStatus::Published,
            'created_at' => 'now()',
            'subsidy_page_url' => 'https://www.dus-i.nl/subsidies/reiskosten-damu-leerlingen-primair-onderwijs',
            'contact_mail_address' => 'damu.dus-i@minvws.nl',
            'mail_to_address_field_identifier' => 'email',
            'mail_to_name_field_identifier' => 'firstName;infix;lastName',
            'review_period' => 7 * 13 // 13 weeks
        ]);
    }
}
