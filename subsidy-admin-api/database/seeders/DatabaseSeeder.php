<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV\BTVSeeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\AIGT\AIGTSeeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU\DAMUSeeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM\PCZMSeeder;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2\PCZMv2Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PCZMSeeder::class);
        $this->call(BTVSeeder::class);
        $this->call(AIGTSeeder::class);
        $this->call(DAMUSeeder::class);
        $this->call(PCZMv2Seeder::class);
    }
}
