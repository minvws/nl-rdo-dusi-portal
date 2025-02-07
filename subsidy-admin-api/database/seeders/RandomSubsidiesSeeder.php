<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class RandomSubsidiesSeeder extends Seeder
{
    public function run(): void
    {
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
                        'mail_to_name_field_identifier' => 'firstName;infix;lastName'
                    ]
                )
                    ->create()
                    ->each(function($subsidyVersion){
                    SubsidyStage::factory(['subsidy_version_id' => $subsidyVersion->id])->create();
                });
            });
    }
}
