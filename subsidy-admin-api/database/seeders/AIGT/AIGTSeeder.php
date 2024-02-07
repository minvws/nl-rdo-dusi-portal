<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;

class AIGTSeeder extends Seeder
{
    public const SUBSIDY_UUID = 'cb91d7d4-6261-4cd6-96e8-d09c86a670b7';
    public const SUBSIDY_VERSION_UUID = '2aaac0da-d265-40bb-bde6-ac20d77e6bca';

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
            'title' => 'Opleidingsactiviteiten arts internationale gezondheid en tropengeneeskunde',
            'reference_prefix' => 'AIGT',
            'code' => 'AIGT',
            'description' => 'Voor artsen in opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde brengt de eindstage van 6 maanden in het buitenland hoge kosten met zich mee. De subsidie Opleidingsactiviteiten AIGT 2021-2026 compenseert deze kosten.',
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
            'subsidy_page_url' => 'https://www.dus-i.nl/subsidies/opleidingsactiviteiten-arts-internationale-gezondheid-en-tropengeneeskunde',
            'contact_mail_address' => 'aigt@minvws.nl',
            'mail_to_address_field_identifier' => 'email',
            'mail_to_name_field_identifier' => 'firstName;infix;lastName',
            'review_period' => 7 * 13 // 13 weeks
        ]);
    }
}
