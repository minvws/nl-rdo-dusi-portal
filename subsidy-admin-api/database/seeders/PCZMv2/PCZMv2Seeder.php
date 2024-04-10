<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM\PCZMSeeder as PCZMSeeder23;

class PCZMv2Seeder extends Seeder
{
    public const VERSION_UUID = '0185f897-99b0-4390-bd1f-98cce4bd578b';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSubsidyVersion();

        $this->call(SubsidyStagesSeeder::class);

        $this->call(ApplicationFieldsSeeder::class);
        $this->call(ApplicationStageUISeeder::class);

        $this->call(AssessmentFieldsSeeder::class);
        $this->call(AssessmentStageUISeeder::class);

        $this->call(SubsidyStageTransitionsSeeder::class);
        $this->call(SubsidyStageTransitionMessageSeeder::class);
    }

    private function createSubsidyVersion(): void
    {
        DB::table('subsidy_versions')->insert(
            [
                'id' => self::VERSION_UUID,
                'subsidy_id' => PCZMSeeder23::PCZM_UUID,
                'version' => 2,
                'status' => VersionStatus::Published,
                'created_at' => '2024-04-01',
                'subsidy_page_url' => 'https://www.dus-i.nl/subsidies/zorgmedewerkers-met-langdurige-post-covid-klachten',
                'contact_mail_address' => 'dienstpostbus@minvws.nl',
                'mail_to_address_field_identifier' => 'email',
                'mail_to_name_field_identifier' => 'firstName;infix;lastName',
                'review_period' => 7 * 13, // 13 weeks
                'review_deadline' => CarbonImmutable::parse('2024-08-30')->endOfDay()->floorSecond()
            ]
        );
    }
}
