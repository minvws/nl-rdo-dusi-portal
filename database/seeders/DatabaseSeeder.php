<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Application;
use App\Models\ApplicationHash;
use App\Models\ApplicationReview;
use App\Models\ApplicationStage;
use App\Models\ApplicationVersion;
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
            ->has(ApplicationVersion::factory(1)
                ->has(ApplicationStage::factory(1)
                    ->has(Answer::factory(1))
                )
            )

            ->create();
    }
}
