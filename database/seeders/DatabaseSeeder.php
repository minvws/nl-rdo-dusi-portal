<?php

declare(strict_types=1);

namespace Database\Seeders;

use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Application::factory(1)
            ->has(ApplicationHash::factory(1))
            ->has(
                ApplicationStage::factory(1)
                    ->has(
                        ApplicationStageVersion::factory(1)
                            ->has(Answer::factory(1))
                    )
            )
            ->create();
    }
}
